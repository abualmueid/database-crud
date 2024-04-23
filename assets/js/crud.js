// JavaScript to handle edit button

jQuery( document ).ready( function( $ ) {
    $( '.edit-btn' ).click( function( e ) {
        e.preventDefault();

        // Show the form
        $( '#data-form' ).css( 'display', 'block' );

        var rowId = $( this ).data( 'row-id' );

        $.post( ajaxurl, {
            action: 'fetch_record',
            row_id: rowId,
            nonce: '<?php echo wp_create_nonce( "fetch_record_nonce" ); ?>'
        }, function( response ) {
            if ( response.success ) {
                var data = response.data;
                $( '#name' ).val( data.name );
                $( '#email' ).val( data.email );
                $( '#save' ).val( 'Update' );
                $( '#row_id' ).val( data.id );
            } else {
                alert( 'Error: Unable to fetch record.' );
            }
        });
    });

    // JavaScript to handle delete button

    $( '.delete-btn' ).click( function( e ) {
        e.preventDefault();

        var rowId = $( this ).data( 'row-id' );
        var confirmDelete = confirm( 'Are you sure you want to delete this record?' );

        if ( confirmDelete ) {
            $.post( ajaxurl, {
                action: 'delete_record',
                row_id: rowId,
                nonce: '<?php echo wp_create_nonce( "delete_record_nonce" ); ?>'
            }, function( response ) {
                if ( response.success ) {
                    $( '#row-' + rowId ).remove();
                    // alert( 'Record deleted successfully!' );
                } else {
                    alert( 'Error: Unable to delete record.' );
                }
            });
        }
    });
});