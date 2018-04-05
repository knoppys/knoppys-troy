<?php
/*
Plugin Name:       Knoppys WP - TROY Integration
Plugin URI:        https://github.com/knoppys/
Description:       
Version:           1.2
Author:            Knoppys Digital Limited
License:           GNU General Public License v2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/knoppys/knoppys-wp-troy.git
GitHub Branch:     master
*/

define( 'PLUGIN_VERSION', '1' );
define( 'PLUGIN__MINIMUM_WP_VERSION', '1.0' );
define( 'PLUGIN__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/***************************
*Load Native & Custom wordpress functionality plugin files. 
****************************/
foreach ( glob( dirname( __FILE__ ) . '*.php' ) as $root ) {
    require $root;
}
foreach ( glob( dirname( __FILE__ ) . '/inc/*.php' ) as $root ) {
    require $root;
}

function troyupdate() {
	if (is_page('home')) {
		troy_categories();
		troy_vacancies();
	}	
}
add_action('wp', 'troyupdate');



//Page for testing
/*
add_action( 'admin_menu', 'my_plugin_menu' );
function my_plugin_menu() {
	add_options_page( 'Testing', 'Testing', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';

	echo troy_vacancies();

	echo '</div>';
}
*/

