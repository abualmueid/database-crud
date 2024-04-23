<?php 

namespace DatabaseCrud;

class AdminMenuPage {
    private $table_name;

    public function __construct( $table_name ) {
        $this->table_name = $table_name;

        // Add admin menu page
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );

        // Function to handle AJAX request for fetching record
        add_action( 'wp_ajax_fetch_record', [ $this, 'fetch_record' ] );

        // Function to handle AJAX request for deleting record
        add_action( 'wp_ajax_delete_record', [ $this, 'delete_record' ] );
    }

    public function add_admin_menu() {
        add_menu_page(
            'Database CRUD',             // Page title
            'Database CRUD',             // Menu title
            'manage_options',            // Capability
            'database-Crud',             // Slug name
            [ $this, 'show_menu_page' ], // Function to show the content of the menu page
            'dashicons-admin-generic'    // Icon url
        );
    }

    public function show_menu_page() {
        $this->display_form();
        $this->add_form_data_to_database();
        $this->display_table();
    }

    public function display_form() {
        echo '<div class="crud-form">';
        echo '<h2>Database CRUD</h2>';
        echo '<p>Welcome to the Database CRUD admin menu page!</p>';

        // Display form
        echo '<button id="add-new-data-btn">Add New Data</button><br><br>';
        echo '<form id="data-form" method="post" style="display: none">';
        wp_nonce_field( 'crud', 'crud_nonce' ); // Initializing nonce
        echo '<label for="name">Name:</label><br>';
        echo '<input type="text" name="name" id="name" value=""><br>';
        echo '<label for="email">Email:</label><br>';
        echo '<input type="email" name="email" id="email" value=""><br><br>';
        echo '<input type="submit" name="submit" id="save" value="Save"><br><br>';
        echo '<input type="hidden" name="row_id" id="row_id" value="">';
        echo '</form>';
    }

    public function add_form_data_to_database() {
        global $wpdb;
        if ( isset( $_POST[ 'submit' ] ) ) {
            // Check and verify nonce
            if ( ! isset( $_POST[ 'crud_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'crud_nonce' ], 'crud' ) ) {
                echo 'Nonce verification failed!';
                return;
            }

            if ( empty( $_POST[ 'name' ] ) || empty( $_POST[ 'email' ] ) ) {
                echo "<h2>Please fill up fields properly!</h2>";
            } else {
                $name = sanitize_text_field( $_POST[ 'name' ] );
                $email = sanitize_email( $_POST[ 'email' ] );

                // Check if the record already exists
                $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE name = %s AND email = %s", $name, $email ) );
    
                if ( $record ) {
                    echo '<h2>Record already exists!</h2>';
                } else {
                    if ( ! empty( $_POST[ 'row_id' ] ) ) {
                        // Update existing record
                        $row_id = intval( $_POST[ 'row_id' ] );
                        $wpdb->update( $this->table_name, [ 'name' => $name, 'email' => $email ], [ 'id' => $row_id ] );
                        echo '<h2>Record updated successfully!</h2>';
                    } else {
                        // Insert new record
                        $wpdb->insert( $this->table_name, [ 'name' => $name, 'email' => $email ] );
                        echo '<h2>Record added successfully!</h2>';
                    }
                }
            }
        }
    }
    
    public function display_table() {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM $this->table_name" );
        ?>

        <br><br>
        <div class="crud-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ( $results as $row ) {
                        echo '<tr id="row-' . $row->id . '">';
                        echo '<td>' . esc_html( $row->id ) . '</td>';
                        echo '<td>' . esc_html( $row->name ) . '</td>';
                        echo '<td>' . esc_html( $row->email ) . '</td>';
                        echo '<td><button class="edit-btn" data-row-id="' . $row->id . '">Edit</button> <button class="delete-btn" data-row-id="' . $row->id . '">Delete</button></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function fetch_record() {
        // Verify nonce
        // if ( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'fetch_record_nonce' ) ) {
        //     wp_send_json_error( 'Invalid nonce' );
        //     return;
        // }

        // check_ajax_referer( 'fetch_record_nonce', 'nonce');

        $row_id = intval( $_POST[ 'row_id' ] ); 
        
        global $wpdb;
        $record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE id = %d", $row_id ) );

        if ( $record ) {
            wp_send_json_success( $record );
        } else {
            wp_send_json_error( 'Unable to fetch record' );
        }
    }

    public function delete_record() {
        // Verify nonce
        // if ( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'delete_record_nonce' ) ) {
        //     wp_send_json_error( 'Invalid nonce' );
        //     return;
        // }

        // check_ajax_referer( 'delete_record_nonce', 'nonce');
        
        $row_id = intval( $_POST[ 'row_id' ] );

        global $wpdb;
        $record = $wpdb->delete( $this->table_name, [ 'id' => $row_id ] );

        if ( $record ) {
            wp_send_json_success( 'Record deleted successfully' );
        } else {
            wp_send_json_error( 'Unable to delete record' );
        }
    }
}



