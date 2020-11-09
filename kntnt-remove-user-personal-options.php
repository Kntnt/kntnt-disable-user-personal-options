<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Remove Personal Option
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Removes the section with personal options from user profiles.
 * Version:           1.0.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


defined( 'ABSPATH' ) || die;

add_action( 'user_edit_form_tag', function () {
    ob_start( function ( $content ) {
        $start = '<h2>' . __( 'Personal Options' ) . '</h2>';
        $stop = '<h2>' . __( 'Name' ) . '</h2>';
        return preg_replace( "`$start.*(?=$stop)`s", '', $content, 1 );
    } );
} );

add_action( 'show_user_profile', 'ob_end_flush' );
add_action( 'edit_user_profile', 'ob_end_flush' );
