<style type="text/css">

span.audiobar_container {
	display: block;
	height: 27px;
	width: 100%;
}
span.audiobar_container span,
span.audiobar_container a {
	display: block;
	float: right;
}

span.audiobar_container a.audiobar_play,
span.audiobar_container span.audiobar_title {
  float: left;
}

a.audiobar_play,
a.audiobar_download {
	margin-left: 6px;
	outline: none;
	text-transform: uppercase;
	text-align: center;
	text-decoration: none;
	font-size: 12px;
	padding: 2px 4px;
	text-shadow: 1px 1px 1px <?php echo $audiobar_button_gradient_2 ?>;
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	border-radius: 7px;
	-webkit-box-shadow: 2px 2px 2px <?php echo $audiobar_button_gradient_2 ?>;
	-moz-box-shadow: 2px 2px 2px <?php echo $audiobar_button_gradient_2 ?>;
	box-shadow: 2px 2px 2px <?php echo $audiobar_button_gradient_2 ?>;
	/* Gradient */
	border: none;
	background: <?php echo $audiobar_button_gradient_2 ?>;
	color: #fff;
	font-weight: bold;
	background: -webkit-gradient(linear, left top, left bottom, from(<?php echo $audiobar_button_gradient_1 ?>), to(<?php echo $audiobar_button_gradient_2 ?>));
	background: -moz-linear-gradient(top,  <?php echo $audiobar_button_gradient_1 ?>,  <?php echo $audiobar_button_gradient_2 ?>);
	display: inline-block;
	margin-bottom: 4px;
}
a.audiobar_download:hover,
a.audiobar_play:hover {
	background: <?php echo $audiobar_button_hilite_gradient_1 ?>;
	background: -webkit-gradient(linear, left top, left bottom, from(<?php echo $audiobar_button_hilite_gradient_1 ?>), to(<?php echo $audiobar_button_hilite_gradient_2 ?>));
	background: -moz-linear-gradient(top,  <?php echo $audiobar_button_hilite_gradient_1 ?>,  <?php echo $audiobar_button_hilite_gradient_2 ?>);
}
a.audiobar_downloadtext {
	color:#666666;
	font-size: 14px;
	line-height: 20px;
	text-decoration: none;
	margin-right: 6px;
}
/* Spacing */
a.audiobar_play {
	margin-right: 6px;
}
span.audiobar_title {
	margin-right: 9px;
}
a.audiobar_downloadtext {
	margin-right: 6px;
}
a.audiobar_download {
	margin-right: 2px;
}
div.audiobar_enable_notice,
div.audiobar_restore_notice {
	width: 250px;
	padding: 10px;
	font-size: 11px;
	background: white;
	border: 1px black solid;
}
div.audiobar_enable_notice {
	position: absolute;
	top: 10px;
	right: 10px;
}
div.audiobar_restore_notice {
	bottom: 10px;
	float: right;
	margin-bottom: 10px;
}

</style>

<!--[if lte IE 8.0]>
<style>
a.audiobar_play,
a.audiobar_download {
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='<?php echo $audiobar_button_gradient_1 ?>', endColorstr='<?php echo $audiobar_button_gradient_2 ?>');
}
a.audiobar_play,
a.audiobar_download {	
  filter:  progid:DXImageTransform.Microsoft.gradient(startColorstr='<?php echo $audiobar_button_hilite_gradient_1 ?>', endColorstr='<?php echo $audiobar_button_hilite_gradient_2 ?>');
}
</style>
<![endif]-->

