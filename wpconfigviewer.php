<?php
// Plugin Name: WPCFG Viewer
// Version: 1.5.3
// Description: Displays the wp-config.php file in a read-only format.

function display_wpconfig() {

  echo '<div class="wrap">';
  echo '<h1>WP Config Viewer</h1>';

  $wp_config = FALSE;
  if ( is_readable( '/www/wp-config.php' ) )
    $wp_config = '/www/wp-config.php';
  elseif ( is_readable( "${_SERVER['DOCUMENT_ROOT']}/wp-config.php" ) )
    $wp_config = "${_SERVER['DOCUMENT_ROOT']}/wp-config.php";
  elseif ( is_readable( ABSPATH . 'wp-config.php' ) )
    $wp_config = ABSPATH . 'wp-config.php';
  elseif ( is_readable( dirname( ABSPATH ) . '/wp-config.php' ) )
    $wp_config = dirname( ABSPATH ) . '/wp-config.php';

  if ( $wp_config )
    $code = esc_html( file_get_contents( $wp_config ) );
  else
    $code = 'wp-config.php not found';

  echo '<div class="wp-config postbox">';
  echo '<h3>Installation path: ' . ABSPATH . '</h3>';
  echo '<textarea rows="80" cols="50" class="large-text code" style="overflow: scroll;max-height: 80vh">';
  echo $code;
  echo '</textarea>';
  echo '</div>';

  echo '</div>'; // end wrap

}


// create new dashboard menu
add_action( 'admin_menu', function() {
  add_menu_page(
    'WP Config Viewer',
    'WPCFG Viewer',
    'manage_options',
    'wp-config-viewer',
    'display_wpconfig',
    'dashicons-admin-generic',
    99
  );
});

add_action('admin_enqueue_scripts', 'codemirror_enqueue_scripts');

function codemirror_enqueue_scripts() {
  $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
  wp_localize_script('jquery', 'cm_settings', $cm_settings);

  wp_enqueue_script('wp-theme-plugin-editor');
  wp_enqueue_style('wp-codemirror');
}

// register and enqueue js file for options page
function wp_config_viewer_scripts() {
  wp_enqueue_script( 'wp-config-viewer-js', plugins_url( '/wp-config-viewer.js', __FILE__ ), ['jquery'], '1.0.0'. filemtime( plugin_dir_path( __FILE__ ) . 'wp-config-viewer.js' ) , true );
}

// register and enqueue css file for options page
function wp_config_viewer_styles() {
  wp_enqueue_style( 'wp-config-viewer-css', plugins_url( '/wp-config-viewer.css', __FILE__ ), [], '1.0.0' . filemtime( plugin_dir_path( __FILE__ ) . 'wp-config-viewer.css' ), 'all' );
}

// if on wp-config-viewer options page, enqueue js and css
if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-config-viewer' ) {
  add_action( 'admin_enqueue_scripts', 'wp_config_viewer_scripts' );
  add_action( 'admin_enqueue_scripts', 'wp_config_viewer_styles' );
}
