<?php // The Audiobar itself ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Audiobar - see http://carlocapocasa.com/tech/audiobar -->
<style type="text/css">
* {
	margin: 0;
}
html {
	background: <?php echo $audiobar_bar_gradient_1 ?>;
	height: 100%;
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='<?php echo $audiobar_bar_gradient_1 ?>', endColorstr='<?php echo $audiobar_bar_gradient_2 ?>');
	background: -webkit-gradient(linear, left top, left bottom, from(<?php echo $audiobar_bar_gradient_1 ?>), to(<?php echo $audiobar_bar_gradient_2 ?>));
	background: -moz-linear-gradient(top,  <?php echo $audiobar_bar_gradient_1 ?>,  <?php echo $audiobar_bar_gradient_2 ?>);
	overflow: hidden;
}
body {
	font-family: Arial, Helvetica, Sans;
	background: <?php echo $audiobar_bar_gradient_1 ?>;
	margin: 0;
	padding: 0;
}
div.player {
	position: absolute;
	left:60px;
	top: 50%;
	margin-top: -16px;
	max-height:32px;
}
audio {
	width: 227px;
	-moz-box-shadow: 3px 3px 7px <?php echo $audiobar_bar_gradient_2 ?>;
}

@-moz-document url-prefix() {
	audio {
		max-height:28px;
	}
	div.player {
		margin-top: -14px;
	}
}


object {
	outline: none
}
div.songtitle {
	text-shadow: 2px 2px 2px <?php echo $audiobar_bar_gradient_2 ?>;
	color: <?php echo $audiobar_title_color ?>;
	left: 336px;
	top: 50%;
	margin-top: -9px;
	font-size: 14px;
	position: absolute;
	font-weight: bold;
	font-style: italic;
	width: 100%;
}
a.backlink {
  display: block;
  position: absolute;
  right: 4px;
  top: 38px;
  color: white;
  font-size: 8px;
  text-decoration: none;
  outline: none;
  text-transform: uppercase;
  opacity: 0.3;
  filter: alpha(opacity=30);
}

div.noscript {
  background-color:#FFFFFF;
  color:#000000;
  font-size:11px;
  padding:2px 5px;
  position:absolute;
  right:10px;
  top:5px;
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	border-radius: 7px;
}
</style>
<?php // Called by the audio tag or the flash player when a song ends ?>
<script type="text/javascript">
function next_song() {
	top.audiobar.play_next('<?php echo $play ?>', <?php echo $forceflash ? 1 : 0 ?>);
}
</script>
</head>
<body onkeypress="return false;"><?php // prohibits key strokes to prevents accidental scrolling ?>
<div class="player">
<?php if (!$forceflash): ?>
<audio id="audio" controls <?php echo $autoplay ? 'autobuffer autoplay' : '' ?> onended="next_song();"><source src="<?php echo get_bloginfo('wpurl') . $play ?>.<?php echo $altogg ? 'oga' : 'ogg' ?>?play" type="audio/ogg">
  <source src="<?php echo get_bloginfo('wpurl') . $play ?>.mp3?play" type="audio/mp3"/>
  <!-- Flash fallback MP3 player by http://flash-mp3-player.net/ -->
<?php endif; ?>
  <div id="wrapper_play">
	<!--[if IE]>
	<object
		id="object_play"
		classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
		type="application/x-shockwave-flash" 
		codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0"
		height="27" width="227" style="margin-top:0px;"><param name="movie" value="<?php echo $swf ?>"><param name="movie" value="<?php echo $swf ?>">
	<![endif]-->
	<!--[if !IE]>-->
	<object
		id="object_play"
		type="application/x-shockwave-flash" 
		data="<?php echo $swf ?>" 
		height="27"
		width="227">
	<!--<![endif]-->
		<param name="wmode" value="transparent">
		<param name="FlashVars" value="mp3=<?php echo audiobar_relative_wpurl() . $play ?>.mp3&amp;width=227&amp;height=27&amp;sliderovercolor=<?php echo substr($audiobar_hover_color, 1) ?>&amp;buttonovercolor=<?php echo substr($audiobar_hover_color, 1) ?>&amp;loadingcolor=<?php echo substr($audiobar_hover_color, 1) ?>&amp;autoplay=<?php echo $autoplay ? 1 : 0 ?>&amp;showvolume=1">
	</object>
	</div>
<?php if (!$forceflash): ?>
</audio>
<?php endif; ?>
</div>
<div class="songtitle">
<?php echo $title ?>
</div>
<!-- Flash "click to activate" fix by http://codecentre.eplica.is/js/eolasfix/test.htm -->
<script defer="defer" src="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/audiobar/lib/eolasfix.js.php" type="text/javascript"></script>
<?php if (!$audiobar_disable_backlink): ?>
<a href="http://carlocapocasa.com/tech/audiobar" class="backlink" target="_blank">
Audiobar
</div>
<?php endif; ?>
<noscript>
<div class="noscript">
<?php _e('Please enable JavaScript') ?>
</div>
</noscript>
</body>	
</html>

