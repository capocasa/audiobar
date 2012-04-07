<?php // The Admin page ?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div>
<h2>Audiobar Settings</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<h3 class="title"><?php _e('Colors') ?></h3>
<table class="form-table">
  <tbody>
<?php foreach ($audiobar_default_colors as $key => $value): ?>
    <tr>
      <th scope="row"><label for="<?php echo $key ?>"><?php echo $labels[$key] ?></label></th>
      <td><input type="text" class="color {hash:true,caps:false} regular-text" id="<?php echo $key ?>" name="<?php echo $key ?>" value="<?php echo get_option($key); ?>"/></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<h3 class="title"><?php _e('Miscellaneous') ?></h3>
<table class="form-table">
  <tbody>
    <tr>
      <th scope="row"><label for="audiobar_disable_backlink"><?php echo $labels['audiobar_disable_backlink'] ?></label></th>
      <td><input type="checkbox" id="audiobar_disable_backlink" name="audiobar_disable_backlink" value="1" <?php if (get_option('audiobar_disable_backlink')) { echo 'checked="checked"'; } ?> /></td>
    </tr>
    <tr>
      <th scope="row"><label for="audiobar_position"><?php echo $labels['audiobar_position'] ?></label></th>
      <td>
        <input type="radio" id="audiobar_position" name="audiobar_position" value="top" <?php if (get_option('audiobar_position') == 'top') { echo 'checked="checked"'; } ?> /> <?php _e('Above content') ?>
        <br />
        <input type="radio" id="audiobar_position" name="audiobar_position" value="bottom" <?php if (get_option('audiobar_position') == 'bottom') { echo 'checked="checked"'; } ?> /> <?php _e('Below content') ?>
      </td>
    </tr>
  </tbody>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="<?php echo $page_options ?>" />

<p class="submit">
<input class="button-primary" type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/audiobar/lib/jscolor/jscolor.js"></script>

