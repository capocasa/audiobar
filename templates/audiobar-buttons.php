<?php // The HTML for the clickable buttons ?>
<span class="audiobar_container">
<?php // audiobar_title must directly follow audiobar_play with no whitespace ?>
<?php if ($show_play_button): ?><a class="audiobar_play" onclick="top.audiobar.collect_playlist('<?php echo $base ?>'); top.play.location.replace(this.href + (top.audiobar.should_force_flash(<?php echo str_replace('"', "'", json_encode($extensions)) ?>) ? '&forceflash' : '')); return false;" href="<?php echo get_bloginfo('wpurl') ?>?audiobar=bar&play=<?php echo $base ?>&title=<?php echo urlencode($title) ?>&autoplay=1<?php if (in_array('oga', $extensions)) echo '&altogg' ?>" target="play" class="play">play</a><?php endif; ?><span class="audiobar_title"><?php echo $title ?></span>
<?php for ($i = count($extensions) - 1; $i >= 0; $i-- ): ?>
<a class="audiobar_download" href="<?php echo audiobar_relative_wpurl(). $base . '.' . $extensions[$i] ?>" class="download"><?php echo $extensions[$i] == 'oga' ? 'ogg' : $extensions[$i] ?></a>
<?php endfor; ?>
<a class="audiobar_downloadtext" href="<?php echo audiobar_relative_wpurl().$base . '.' . $extensions[0] ?>" class="text"><?php _e("Download") ?></a>
</span>
