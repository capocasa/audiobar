<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<style type="text/css">
	html,body {
		height: 100%;
		overflow: hidden;
	}
	* {margin:0;padding:0;} 
	iframe#iframe_play {
		border: 0;
		width: 100%;
		height: 8%;
	}
	iframe#iframe_content {
		border: 0;
		height: 92%;
		width: 100%;
	}
	</style>
	<?php echo $title_tag // include title from template header ?>
	<?php echo $meta_tags // include meta tags from template header ?>
</head>
<body>

<!--[if IE 9]>
<script type="text/javascript">
ie9 = true;
</script>
<![endif]-->

<?php if (get_option('audiobar_position') == 'top'): ?>
<iframe id="iframe_play" name="play" frameborder="0" src="<?php echo $play_url ?>"></iframe>
<?php endif; ?>

<iframe id="iframe_content" name="content" frameborder="0" src="<?php echo $content_url ?>" >

<?php echo $audiobar_seo_content ?>
</iframe>
<?php if (get_option('audiobar_position') == 'bottom'): ?>
<iframe id="iframe_play" name="play" frameborder="0" src="<?php echo $play_url ?>"></iframe>
<?php endif; ?>

<script type="text/javascript">

	var audiobar = {
		playlist: null,
		param: '<?php echo AUDIOBAR_FRAMEPARAMETER ?>',
		wpurl: '<?php echo get_bloginfo('wpurl') ?>',
		height: <?php echo AUDIOBAR_BAR_HEIGHT ?>,
		adapt_content_height: function () {
			var play = document.getElementById('iframe_play'), content = document.getElementById('iframe_content');
			var height = document.documentElement.clientHeight;
			play.style.height = audiobar.height+'px';
			content.style.height = (height - audiobar.height) + 'px';
		},
		play_next: function (base, forceflash) {
			if (!this.playlist) {
				return;
			}
			var next_song = null;
			for (var i = 0; i < this.playlist.length; i++) {
				if (this.playlist[i].base == base) {
					if (this.playlist[i + 1]) {
						next_song = this.playlist[i + 1];
					} else {
						next_song = this.playlist[0];
					}
					break;
				}
			}
			if (next_song && this.start_base != next_song.base) {
				window.play.location.replace(audiobar.wpurl + '?audiobar=bar&play='+next_song.base+'&title='+this.urlencode(next_song.title)+'&autoplay=1' + (next_song.altogg ? '&altogg=1' : '') + (forceflash ? '&forceflash=1' : ''));
			}
		},
		collect_playlist: function (start_base) {
			var elements = window.content.document.getElementsByTagName('a');
			this.playlist = [];
			var base, altogg;
			for (var i  = 0; i < elements.length; i++) {
				if (elements[i].className == 'audiobar_play') {
					base = elements[i].href;
					altogg = base.indexOf('altogg') != -1;
					base = base.substr(base.indexOf('play=')+5);
					base = base.substr(0, base.indexOf('&title='));
					this.playlist.push({base: base, title: elements[i].nextSibling.innerHTML, altogg: altogg});
				}
			}
			if (start_base) {
			  this.start_base = start_base;
			}
		},
		get_url_base: function (url) {
			if (url.indexOf('http://') == 0) {
				url = url.substr(7);
			}
			if (url.indexOf('https://') == 0) {
				url = url.substr(8);
			}
			url = url.substr(url.indexOf('/'));
			url = url.substr(0, url.lastIndexOf('.'))
			return url;
		},
		urlencode: function (str) {
			// urlencode by http://phpjs.org/functions/urlencode
		  str = (str+'').toString();
		  return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
		},
		poll: function () {
		  // Container to Frame poll
			if (!audiobar.suspend_poll && audiobar.topHref != window.location.href) {
				var n = window.location.href.indexOf('#');
				if (n != -1) {
					var newUrl = window.location.href;
          var frag = newUrl.substr(n+2);
			    var m = frag.indexOf('!');
          if (m != -1) {
  					frag = frag.substr(0,m)+'#'+frag.substr(m+1);
          }
					newUrl = newUrl.substr(0,n)+frag;
					if (newUrl.substr(n) == '') {
					  newUrl += '?'+audiobar.param;
					}

//			    if (newUrl.substr(-1) != '/') {
//			      newUrl += '/';
//			    }

					audiobar.suspend_poll = true;
					if (audiobar.ie) {
					  // IE records no history on anchor changes, generate with IFrame
						window.content.location.href = newUrl;
					} else {
					  // Other browsers generate history on anchor changes, suppress for IFrame
						window.content.location.replace(newUrl);
    			}
					audiobar.contentHref = newUrl;
					audiobar.topHref = window.location.href;
				}
			}

			if (!audiobar.suspend_poll && audiobar.contentHref != window.content.location.href) {
  		  // Frame to container poll
				var newUrl = window.content.location.href;
				audiobar.contentHref = newUrl;
				var n = audiobar.wpurl.length;
				var frag = newUrl.substr(n);
		    var m = frag.indexOf('#');
        if (m != -1) {
					frag = frag.substr(0,m)+'!'+frag.substr(m+1);
        }
				newUrl = newUrl.substr(0, n) + '/#' + frag;
				n = newUrl.indexOf(audiobar.param);
				if (n != -1) {
				  newUrl = newUrl.substr(0, n-1);
				}
				// Suppress URL adaption for very first load of front page
				if (!((window.location.href == audiobar.wpurl || window.location.href.substr(0, window.location.href.length - 1) == audiobar.wpurl) && newUrl.substr(-2) == '#/')) {
  				window.location.replace(newUrl);
  			}
				audiobar.topHref = newUrl;
			}
		},
		should_force_flash: function (extensions) {
      var a = document.createElement('audio');
		  
      // Force flash for so-so audio tag implentations
      if (window.ie9) {
        return true;
      }

      // Force flash if the browser needs ogg and no ogg is provided
		  if (extensions.length == 0) {
		    return false;
		  }
		  for (var i = 0; i < extensions.length; i++) {
		    if (extensions[i] == 'ogg' || extensions[i] == 'oga') {
		      return false;
		    }
		  }
		  if (a.canPlayType && a.canPlayType('audio/mp3')) {
		    return false;
		  }
		  return true;
		}
	}

  if (window != top) {
    window.location.replace(audiobar.wpurl+'?'+audiobar.param);
  }

</script>
<!--[if IE]>
<script type="text/javascript">
	audiobar.ie = true;
</script>
<![endif]-->
<script defer="defer" type="text/javascript">
	audiobar.adapt_content_height();
</script>
<script type="text/javascript">
	window.onresize = audiobar.adapt_content_height;
</script>
<script type="text/javascript">
  // Don't trigger polls on init
	audiobar.contentHref = window.content.location.href;
	//audiobar.topHref = audiobar.param+'/#/';
  // Poll for relaying container URL to content frame URL and vice versa
	window.setInterval(audiobar.poll, 250);
  // Flash fallback for browsers that support the audio element but not the codec
  audiobar.first_extensions = <?php echo json_encode($audiobar_first_extensions) ?>;
	audiobar.start_base = '<?php echo $audiobar_first_base ?>';
  audiobar.first_play_url = '<?php echo $play_url	?>';
  if (audiobar.should_force_flash(audiobar.first_extensions)) {
	  window.play.location.replace(audiobar.first_play_url + '&forceflash=1');
	}
</script>
</body>	

</html>

