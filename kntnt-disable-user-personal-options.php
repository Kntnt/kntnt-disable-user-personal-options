<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Disable User's Personal Option
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Disables the section with personal options from user profiles, and resets personal options on save.
 * Version:           1.1.2
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Kntnt\Remove_User_Personal_Options;


defined( 'ABSPATH' ) && new Plugin;


class Plugin {

    private $user_id;

    private $meta_value_defaults = [
        'rich_editing' => 'true', // String value 'true' or 'false'
        'syntax_highlighting' => 'true', // String value 'true' or 'false'
        'comment_shortcuts' => 'false', // String value 'true' or 'false'
        'admin_color' => 'fresh', // String value 'fresh' gives default theme
        'use_ssl' => true, // Boolean value
        'locale' => '', // Empty = default locale
    ];

    private $no_admin_bar_front_roles = [
        'subscriber',
    ];

    public function __construct() {
        if ( is_admin() ) {
            add_action( 'plugins_loaded', [ $this, 'run' ] );
        }
    }

    public function run() {

        $this->meta_value_defaults = apply_filters( 'kntnt-remove-user-personal-options/meta-value-defaults', $this->meta_value_defaults );

        $this->no_admin_bar_front_roles = apply_filters( 'kntnt-remove-user-personal-options/no-admin-bar-front-roles', $this->no_admin_bar_front_roles );

        add_filter( 'get_object_subtype_user', [ $this, 'set_user_id' ], 10, 2 );

        add_filter( 'user_edit_form_tag', [ $this, 'user_edit_form' ], 10, 1 );
        add_action( 'show_user_profile', 'ob_end_flush', 10, 0 );
        add_action( 'edit_user_profile', 'ob_end_flush', 10, 0 );

        add_filter( 'sanitize_user_meta_rich_editing', [ $this, 'user_meta' ], 10, 2 );
        add_filter( 'sanitize_user_meta_syntax_highlighting', [ $this, 'user_meta' ], 10, 2 );
        add_filter( 'sanitize_user_meta_comment_shortcuts', [ $this, 'user_meta' ], 10, 2 );
        add_filter( 'sanitize_user_meta_admin_color', [ $this, 'user_meta' ], 10, 2 );
        add_filter( 'sanitize_user_meta_use_ssl', [ $this, 'user_meta' ], 10, 2 );
        add_filter( 'sanitize_user_meta_locale', [ $this, 'user_meta' ], 10, 2 );

        add_filter( 'sanitize_user_meta_show_admin_bar_front', [ $this, 'user_meta_show_admin_bar_front' ], 10, 2 );

    }

    public function set_user_id( $object_subtype, $object_id ) {
        $this->user_id = $object_id;
        return $object_subtype;
    }

    public function user_edit_form() {
        ob_start( function ( $content ) {
            $start = '<h2>' . __( 'Personal Options' ) . '</h2>';
            $stop = '<h2>' . __( 'Name' ) . '</h2>';
            return preg_replace( "`$start.*(?=$stop)`s", '', $content, 1 );
        } );
    }

    public function user_meta( $meta_value, $meta_key ) {
        return isset( $this->meta_value_defaults[ $meta_key ] ) ? $this->meta_value_defaults[ $meta_key ] : $meta_value;
    }

    public function user_meta_show_admin_bar_front( $meta_value ) {
        $user = get_user_by( 'id', $this->user_id );
        return array_diff( $user->roles, $this->no_admin_bar_front_roles ) ? 'true' : 'false';
    }

}
