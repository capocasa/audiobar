<?php
/*
Plugin Name: Audiobar
Plugin URI: http://carlocapocasa.com/tech/audiobar
Description: Audiobar is a stylish audio player bar that continues playing when visitors browse.
Version: 1.0.3
Author: Carlo Capocasa
Author URI: http://carlocapocasa.com
License: GPL2

Copyright 2010  Carlo Capocasa  (email : carlo@carlocapocasa.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * When on the wordpress frontpage, attaching this parameter as a GET variable
 * tells audiobar to load the content rather than the iframe container.
 */
define('AUDIOBAR_FRAMEPARAMETER', 'audiobar-home');

/**
 * 
 */
function audiobar_settings() {
  require(audiobar_get_template('audiobar-settings.php'));
}
add_action('init', 'audiobar_settings');


/**
 * Sets headers to cache for a number of days
 * @param int days Days to cache for
 */
function cache_for($days) {
  $seconds = $days * 86400;
	header('Expires: ' . date('D, d M Y H:i:s',time()+$seconds) . ' GMT');
	header("Cache-Control: max-age=$seconds");
  if (function_exists('header_remove')) {
    header_remove('Pragma');
  }
}


/**
 * Wraps the blog into the audio frameset on the home URL
 * @param string $url Original home URL
 * @return string Modified home URL
 */
function audiobar_container() {
	$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	if (rtrim($url, '/') != rtrim(get_bloginfo('url'), '/') || isset($_GET[AUDIOBAR_FRAMEPARAMETER]) || false !== strpos( $_SERVER['REQUEST_URI'], AUDIOBAR_FRAMEPARAMETER )) {
    $_SERVER['REQUEST_URI'] = '/';
    unset( $_GET[AUDIOBAR_FRAMEPARAMETER] );
    unset( $_REQUEST[AUDIOBAR_FRAMEPARAMETER] );
		return;
	}

	global $audiobar_first_base, $audiobar_first_title, $audiobar_first_extensions;
	// Loads alternative content for SEO
	// This also makes a note of the first link in the page for audiobar's initial song
	ob_start();
	include(audiobar_get_template('audiobar-seo-content.php'));
	$audiobar_seo_content = ob_get_contents();
	ob_end_clean();

  $play_url = get_bloginfo( 'url' ) .'/?audiobar=bar&play=' . urlencode(in_array('mp3', $audiobar_first_extensions) ? $audiobar_first_base : '') .'&title=' . urlencode($audiobar_first_title) . (in_array('oga', $audiobar_first_extensions) ? '&altogg=1' : '');

  $content_url = get_bloginfo( 'url') . '/?' . AUDIOBAR_FRAMEPARAMETER;

	ob_start();
  $header_template = locate_template( array( 'header.php' ));
  if ('' != $header_template) {
    include($header_template);
  }
	$header_content = ob_get_contents();
	ob_end_clean();

  preg_match_all('#<meta[^<>]*>#', $header_content, $meta_tags);
  $meta_tags = implode("\n", $meta_tags[0]);

  preg_match_all('#<title[^<>]*>[^<>]*<\s*/\s*title\s*>#', $header_content, $title_tag);
  $title_tag = $title_tag[0][0];

  unset($header_content);

	include(ABSPATH.'/wp-content/plugins/audiobar/audiobar-container.php');
	die();
}
add_action('template_redirect', 'audiobar_container');


/**
 * Displays the audio bar frame
 * @param object $wp_query The wordpress query object
 */
function audiobar_bar( $wp_query ) {

	if ( !isset($_GET['audiobar']) || 'bar' != $_GET['audiobar'] ) {
		return;
	}

	$autoplay = isset($_GET['autoplay']) ? $_GET['autoplay'] : 0;

	$title = $_GET['title'];
	$play = $_GET['play'];
	if ($play == '') {
	  // No URL lets FF audio player disappear
	  // With an MP3 and OGG containing silence, the player is there
	  $play = '/wp-content/plugins/audiobar/assets/audiobar-silence';
	  $title = '--';
	}
	$swf = get_bloginfo( 'wpurl' ) . '/wp-content/plugins/audiobar/assets/player_mp3_maxi.swf';
  $forceflash = isset($_GET['forceflash']);
  $altogg = isset($_GET['altogg']);
  $audiobar_disable_backlink = get_option('audiobar_disable_backlink');

  include(ABSPATH.'/wp-content/plugins/audiobar/audiobar-default-settings.php');
  foreach ($audiobar_default_colors as $key => $value) {
    $$key = get_option($key); // Set template variables
  }

	include(audiobar_get_template('audiobar-bar.php'));
	die();
}
add_action( 'init', 'audiobar_bar');

/**
 * Inserts audiobar's styles and script into the header
 */
function audiobar_headtags() {
  include(ABSPATH.'/wp-content/plugins/audiobar/audiobar-default-settings.php');
  foreach ($audiobar_default_colors as $key => $value) {
    $$key = get_option($key); // Set template variables
  }
	include(audiobar_get_template('audiobar-buttons-css.php'));
}
add_action('wp_head', 'audiobar_headtags');


/**
 * A regex that matches links to audio files in the post and returns the link as first backreference,
 * the linked text as second
 */
define('AUDIOBAR_REGEX_AUDIO_FILE_LINKS', '#<a\s+[^<>]*\s*href\s*=\s*["\']([^"\']*)\.(ogg|oga|flac|mp3|wav)["\']\s*>([^<>]*)<\s*/\s*a\s*>#');

/**
 * Replaces links to audio files with audiobar controls
 * @param string $content The post content
 * @return string Modified post content
 */
function audiobar_replace_audio_links($content) {
	return preg_replace_callback(AUDIOBAR_REGEX_AUDIO_FILE_LINKS, 'audiobar_callback', $content);
}
add_action('the_content', 'audiobar_replace_audio_links');

/**
 * Used to make a note of the first replaced link
 */
$audiobar_first_page = null;
$audiobar_first_title = null;
$audiobar_first_extensions = array();

/**
 * Callback function for audiobar_replace_audio_links, replaces a single link
 * @param array $matches Matches array as passed by preg_replace_callback
 * @return string Replacement
 */
function audiobar_callback( $matches ) {
	global $audiobar_first_base, $audiobar_first_title, $audiobar_first_extensions;
	// Extract information
	$base = substr( $matches[1], strlen(get_bloginfo('wpurl')));

	$linked = $matches[3];
	
	$extensions = array();
	$possibleExtensions = array( 'ogg','oga','flac','wav','mp3' );
	foreach ( $possibleExtensions as $extension ) {
		if ( file_exists( ABSPATH . $base . '.' . $extension ) ) {
			$extensions[] = $extension;
		}
	}

	$title = audiobar_get_title( $base, $extensions, $linked );

	if ($audiobar_first_base === null) {
		$audiobar_first_base = $base;
		$audiobar_first_title = $title;
		$audiobar_first_extensions = $extensions;
	}

	return audiobar_get_buttons( $base, $title, $extensions );
}

/**
 * Retrieves the buttons template and returns its markup
 *
 * @param string $base Relative path of the audio file, excluding extension
 * @param string $title Track title
 * @param array $extensions Extensions of available file formats for this track
 * @return string Markup of audio bar buttons for one track
 */

function audiobar_get_buttons( $base, $title, $extensions ) {
  // MP3 is required to play files in the browser.
  $show_play_button = in_array('mp3', $extensions);

	ob_start();
	include(audiobar_get_template('audiobar-buttons.php'));
	$buttons = ob_get_contents();
	ob_end_clean();
	return $buttons;
}

/**
 * Initializes a getID3 object
 */
function audiobar_make_getid3() {
  include_once(ABSPATH.'/wp-content/plugins/audiobar/lib/getid3/getid3.php');
  include_once(ABSPATH.'/wp-content/plugins/audiobar/lib/getid3/extension.cache.mysql.php');
  
  global $table_prefix;

  $getID3 = new getID3_cached_mysql(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, audiobar_get_cache_table());
  
#  $getID3 = new getID3;

  return $getID3;
}


/**
 * Retrieves the title meta tag for an audio file designated by a relative path
 *
 * @param string $relative Relative path of audio file
 * @return string Track title from metadata
 */
function audiobar_get_title( $base, $extensions, $fallback ) {
	// Retrieve track title from audio file meta tags

  $title = false;

  $getID3 = audiobar_make_getid3();

  foreach ($extensions as $extension) {
	  $abspath = ABSPATH.$base.'.'.$extension;
	  if (!file_exists($abspath)) {
      continue;
	  }
    $fileinfo = $getID3->analyze($abspath);
    getid3_lib::CopyTagsToComments($fileinfo);
 
    if (isset($fileinfo['comments_html']) && isset($fileinfo['comments_html']['title'])) {
      $title = $fileinfo['comments_html']['title'][0];
    }

    if ($title == '') {
      continue;
	  }
	  return $title;
  }
	return $fallback;
}

/**
 * Redirects the user to the audiobar wrapper
 * @param string $url Original home URL
 * @return string Modified home URL
 */
function audiobar_page_js() {
	$url = get_bloginfo('url');
	$slug = str_replace($url, '', get_permalink());
	if ( false === strpos( $slug, AUDIOBAR_FRAMEPARAMETER ) ) {
		$url .= '/#' . $slug;
	}
	include(audiobar_get_template('audiobar-page-js.php'));
}
add_action('wp_footer', 'audiobar_page_js');

/**
 * Finds an audiobar template file either in the current theme directory
 * or the audiobar template directory.
 * @param string $filename File name of the template
 * @return string $template Full path of the template
 */
function audiobar_get_template($filename) {
	$template = locate_template( array( $filename ));
	if ( '' == $template ) {
		$template = ABSPATH . '/wp-content/plugins/audiobar/templates/'.$filename;
	}
	return $template;
}

define('AUDIOBAR_HTACCESS_RULES', '
<IfModule mod_headers.c>
<FilesMatch "\.(mp3|ogg|oga|flac|wav)">
	Header set Content-Disposition attachment
</FilesMatch>
</IfModule>
');

/**
 * Automatically configures OGG, OGA, MP3, FLAC, WAV files as downloads
 * on Apache servers on plugin activation, or displays a notice.
 *
 * This function based on save_mod_rewrite_rules() in wp-admin/includes/misc.php
 * of the core Wordpress installation.
 */
function add_htaccess_rules($reverse = false) {
	if ( is_multisite() )
		return;

  global $is_apache;
  
  if (!$is_apache) {
    return;
  }

	$home_path = get_home_path();
	$htaccess_file = $home_path.'.htaccess';

	if ((!file_exists($htaccess_file) && is_writable($home_path)) || is_writable($htaccess_file)) {
	  $rules = $reverse ? array() : explode("\n", AUDIOBAR_HTACCESS_RULES);
		insert_with_markers( $htaccess_file, 'Audiobar', $rules);
	}
}

/**
 * Initialize the plugin on activation
 */
function audiobar_activation() {
  include(ABSPATH.'/wp-content/plugins/audiobar/audiobar-default-settings.php');
  foreach($audiobar_default_colors as $key => $value) {
    add_option($key, $value, '', 'yes');
  }
  add_option('audiobar_disable_backlink', 0, '', 'yes');
  add_option('audiobar_position', 'top', '', 'yes');
  add_option('audiobar_autoplay', 0, '', 'yes');
  add_option('audiobar_cache_table', $audiobar_default_cache_table, '', 'yes');
  add_htaccess_rules();
  $getID3 = audiobar_make_getid3();
  $getID3->clear_cache();
}
register_activation_hook(__FILE__, 'audiobar_activation');

/**
 * Remove plugin data on deactivation
 */
function audiobar_deactivation() {
  add_htaccess_rules(true);
  global $wpdb, $table_prefix;
  $wpdb->query("DROP TABLE " . audiobar_get_cache_table());
  delete_option('audiobar_cache_table');
}
register_deactivation_hook(__FILE__, 'audiobar_deactivation');

/**
 * Puts together the table Audiobar uses for caching
 * extracted track information
 *
 * This table gets emptied and dropped, hence
 * forced to start with $table_prefix . 'audiobar_' as a safety
 */
function audiobar_get_cache_table() {
  global $table_prefix;
  return $table_prefix . 'audiobar_' . get_option('audiobar_cache_table');
}

/**
 * Admin Menu
 */
function audiobar_admin_menu() {
  add_options_page('Audiobar', 'Audiobar', 'administrator', 'audiobar', 'audiobar_admin');
}
if ( is_admin() ){
  add_action('admin_menu', 'audiobar_admin_menu');
}

/**
 * Admin Menu Markup
 */
function audiobar_admin() {
  include(ABSPATH.'/wp-content/plugins/audiobar/audiobar-default-settings.php');

  $labels = array(
	  'audiobar_title_color' => __('Color of song title'),
	  'audiobar_hover_color' => __('Color for hovering over the flash player'),
	  'audiobar_bar_gradient_1' => __('Top color for bar gradient'),
	  'audiobar_bar_gradient_2' => __('Bottom color for bar gradient'),

	  'audiobar_button_gradient_1' => __('Top color for button gradient'),
	  'audiobar_button_gradient_2' => __('Bottom color for button gradient'),
	  'audiobar_button_hilite_gradient_1' => __('Top color for highlighted button gradient'),
	  'audiobar_button_hilite_gradient_2' => __('Bottom color for highlighted button gradient'),
	  'audiobar_disable_backlink' => __('Check here to disable the audiobar link'),
	  'audiobar_position' => __('The audiobar is displayed:'),
  );

  $page_options = array_keys($labels);
  $page_options = implode(',', $page_options);

	include(audiobar_get_template('audiobar-admin.php'));
}

/**
 * Display note if automatic configuration for audio file downloads has been flagged
 * to need need some manual attention.
 */

function audiobar_notices() {
  global $is_apache;

  if (!$is_apache) {
    $audiobar_notice = 'no_apache';
  } elseif ( ! apache_mod_loaded( 'mod_headers', true ) ) {
    $audiobar_notice = 'no_mod_headers';
	} elseif (!file_exists($htaccess_file) && !is_writable($home_path)) {
    $audiobar_notice = 'homedir_not_writable';
	} elseif (file_exists($htaccess_file) && !is_writable($htaccess_file)) {
    $audiobar_notice = 'htaccess_not_writable';
	}

  if (get_option('audiobar_notice') != '') {
    $messages = array(
      'no_apache' => __('If your download links start playing in the browser instead of downloading,please configure the file types <code>ogg</code>,<code>oga</code>,<code>mp3</code>,<code>flac</code> and <code>wav</code> to have the header <code>Content-disposition=attachment</code>.'),
      'no_mod_headers' => __('If your download links start playing in the browser instead of downloading, please make sure your Apache has the module <code>mod_headers</code> enabled.'),
      'htaccess_not_writable' => __('If your download links start playing in the browser instead of downloading, please manually add the following code to your <code>.htaccess</code> file, as it is not writable to Wordpress.') . '<pre>'.AUDIOBAR_HTACCESS_RULES.'</pre>',
      'htaccess_not_writable' => __('If your download links start playing in the browser instead of downloading, please manually create and add the following code to your <code>.htaccess</code> file, as your home directory is not writable to Wordpress.').'<pre>'.AUDIOBAR_HTACCESS_RULES.'</pre>',
    );
    $message = __('Thank you for installing Audiobar!') . $messages[$audiobar_notice] . __('Your hosting provider may be able to help you with this.');
    echo sprintf('<div class="updated"><p>%s</p></div>', $message);
  }
}

/**
 * Returns wpurl as a relative url
 * @return string WPURL as relative URL
 */
function audiobar_relative_wpurl() {
  $url = get_bloginfo('wpurl');
  return substr($url, strpos($url, '/', 9));
}

/**
 * Allows for the uploading of flac files
 * @param array $mimes Original array of mime types
 * @return array Augmented array of mime types
 */
function allow_flac($mimes) {
  $mimes = array_merge($mimes, array(
      'flac' => 'audio/flac'
  ));
  return $mimes;
}
add_filter('upload_mimes', 'allow_flac');

