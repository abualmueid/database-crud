jQuery( document ).ready( function( $ ) {
    // Show the form when the button is clicked
    $( '#add-new-data-btn' ).click( function() {
        $( '#data-form' ).show();
        // $('#data-form').css('display', 'block');
    });

    // Validate form
    $( '#data-form' ).submit( function( e ) {
        // Prevent form submission
        // e.preventDefault();

        // Check if name and email fields are empty
        var name = $( '#name' ).val().trim();
        var email = $( '#email' ).val().trim();

        if ( name === '' || email === '' ) {
            alert( 'Please fill up fields properly!' );
            return false; // Prevent form submission
        }

        // If fields are not empty, proceed with form submission
        $( this ).off( 'submit' ).submit();
    });

});