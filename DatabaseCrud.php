<?php 

/**
 * Plugin Name: Database Crud
 * Plugin URI: https://google.com 
 * Description: Aim of this plugin is to do CRUD operation in WP Database.
 * Version: 1.0.0
 * Author: Abu Al Mueid
 * Author URI: https://github.com/abualmueid
 */

// Exit if accessed directly 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require 'vendor/autoload.php';

use DatabaseCrud\CrudOperation;
use DatabaseCrud\AdminMenuPage;

if ( ! class_exists( 'DatabaseCrud' ) ) {
    class DatabaseCrud {
        private $table_name;

        public function __construct() {
            add_action( 'init', [ $this, 'init' ] );
        }

        public function init() {
            // Define custom table name
            global $wpdb;
            $this->table_name = $wpdb->prefix . 'custom_table';

            new CrudOperation( __FILE__, $this->table_name );
            new AdminMenuPage( $this->table_name );

            // Add css file for admin menu page
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_style' ] );        
            
            // Add js file for admin menu page
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_script' ] );

            // Add ajax url for admin menu page
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_ajax_script' ] );
        }

        public function enqueue_style() {
            wp_enqueue_style( 'crud-style', plugin_dir_url( __FILE__ ) . 'assets/css/crud.css' );
        }

        public function enqueue_script() {
            wp_enqueue_script( 'crud-script', plugin_dir_url( __FILE__ ) . 'assets/js/crud.js' );
            wp_enqueue_script( 'form-script', plugin_dir_url( __FILE__ ) . 'assets/js/form.js' );
        }

        public function enqueue_ajax_script() {
            wp_localize_script( 'crud-ajax-script', 'crud_ajax', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
        }
    }

    new DatabaseCrud();
}