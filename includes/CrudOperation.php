<?php 

namespace DatabaseCrud;

class CrudOperation {
    private $plugin_file;
    private $table_name;
    private $database_version = '1.1.1';
    
    public function __construct( $plugin_file, $table_name ) {
        $this->plugin_file = $plugin_file;
        $this->table_name = $table_name;

        // Create table while activating the plugin
        // register_activation_hook( $this->plugin_file, [ $this, 'create_table' ] );

        // Update database version
        $database_version = get_option( 'database_version' );
        if ( $database_version != $this->database_version ) {
            $this->create_table();
            update_option( 'database_version', $this->database_version );
        }

        // Drop table while deactivating the plugin
        register_deactivation_hook( $this->plugin_file, [ $this, 'drop_table' ] );

        
    }

    public function create_table() {
        // Get the charset collate
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Create table
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(20) NOT NULL,
            email varchar(20) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // Insert dummy data
        $wpdb->insert( $this->table_name, [ 'name' => 'Tanjim', 'email' => 'tanjim@gmail.com' ] );
        $wpdb->insert( $this->table_name, [ 'name' => 'Rasel', 'email' => 'rasel@gmail.com' ] );
        $wpdb->insert( $this->table_name, [ 'name' => 'Faruk', 'email' => 'faruk@gmail.com' ] );
    }

    public function drop_table() {
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS $this->table_name" );
    }
}