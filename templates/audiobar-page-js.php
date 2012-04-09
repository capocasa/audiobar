<script type="text/javascript">
	(function () {
  	var wpurl = '<?php echo get_bloginfo('wpurl') ?>';
    if (top.location == window.location) {
	    // dom ready advice by Arnout Kazemier ( blog.3rd-Eden.com )
      window.location.replace(wpurl + '/#' + window.location.href.replace(wpurl, ''));
  	}
  	var links = document.getElementsByTagName('a');
  	for (var i = 0; i < links.length; i++) {
  	  if (!links[i].target && links[i].href.indexOf(wpurl) == -1) {
  	    links[i].target = '_top';
  	  } else if (links[i].href == wpurl) {
  	    links[i].href += '/?<?php echo AUDIOBAR_FRAMEPARAMETER ?>';
  	  } else if (links[i].href == wpurl + '/') {
  	    links[i].href += '?<?php echo AUDIOBAR_FRAMEPARAMETER ?>';
  	  }
  	}
 		top.audiobar.collect_playlist('<?php $audiobar_first_base ?>');
 		top.audiobar.suspend_poll = false;
	})();	
</script>

