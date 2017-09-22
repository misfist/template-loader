<?php
/**
 * Template Loader
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/misfist/template-loader
 * @since             1.0.0
 * @package           Pea_Template_Loader
 */

/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/templates/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/plugin/templates/$template_name.
 *
 * @since 1.0.0
 *
 * @param   string  $template_name          Template to load.
 * @param   string  $string $template_path  Path to templates.
 * @param   string  $default_path           Default path to template files.
 * @return  string                          Path to the template file.
 */
function pea_locate_template( $template_name, $template_path = '', $default_path = '' ) {
  // Set variable to search in the templates folder of theme.
  if ( ! $template_path ) {
    $template_path = trailingslashit( 'templates' );
  }

  // Set default plugin templates path.
  if ( ! $default_path ) {
    $default_path = plugin_dir_path( __FILE__ ) . $template_path; // Path to the template folder
  }

  // Search template file in theme folder.
  $template = locate_template( array(
    $template_path . $template_name,
    $template_name
  ) );

  // Get plugins template file.
  if ( ! $template ) {
    $template = $default_path . $template_name;
  }
  return apply_filters( 'pea_locate_template', $template, $template_name, $template_path, $default_path );
}

/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 1.0.0
 *
 * @see pea_locate_template()
 *
 * @param string  $template_name          Template to load.
 * @param array   $args                   Args passed for the template file.
 * @param string  $string $template_path  Path to templates.
 * @param string  $default_path           Default path to template files.
 */
function pea_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {
  if ( is_array( $args ) && isset( $args ) ) {
    extract( $args );
  }

  $template_file = pea_locate_template( $template_name, $tempate_path, $default_path );

  if ( ! file_exists( $template_file ) ) {
    new WP_Error( 'No Template File', sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
    return;
  }
  
  include $template_file;
}

/**
 * Template loader.
 *
 * The template loader will check if WP is loading a template
 * for a specific Post Type and will try to load the template
 * from out 'templates' directory.
 *
 * @since 1.0.0
 *
 * @param string  $template Template file that is being loaded.
 * @return  string          Template file that should be loaded.
 */
function pea_template_loader( $template ) {
  $find = array();
  $file = '';

  if( is_singular() ) {
    $file = 'single-plugin.php';
  } elseif( is_tax() ) {
    $file = 'archive-plugin.php';
  }

  if ( file_exists( pea_locate_template( $file ) ) ) {
    $template = pea_locate_template( $file );
  }

  return $template;
}
add_filter( 'template_include', 'pea_template_loader' );
