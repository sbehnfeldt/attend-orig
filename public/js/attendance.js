;(function ( global, $ ) {
    'use strict';

    let SigninTab = (function ( selector ) {
        let $tab     = $( selector );
        let $form    = $tab.find( 'form' );
        let $weekOf  = $form.find( 'input[name=week-of]' );
        let $dark    = $form.find( 'input[type=checkbox]' );
        let $options = $form.find( 'button[name=options]' );
        let $pdf     = $form.find( 'button[name=pdf]' );

        $options.on( 'click', function () {
            $form.find( '.options' ).toggle();
        } );
        $pdf.on( 'click', function () {
            window.location = 'signin.php?week=' + $weekOf.val() + '&' + $dark.serialize();
        } );
        $form.on( 'submit', function () {
            return false;
        } );

    })( '#signin-tab' );


    $( function () {
        console.log( 'Document loaded.' );
        $( 'table.attendance-table' ).DataTable( {
            'order'     : [ [ 0, 'asc' ] ],
            'info'      : false,
            'paging'    : false,
            'search'    : false,
            'columnDefs': [
                { targets: [ 0 ] },
                { targets: '_all', sortable: false }
            ]
        } );

        let $weekOf = $( 'input[name=week-of]' );
        $weekOf.datepicker();
        $( 'table.attendance-table td' ).each( function ( idx, td ) {
            if ( !$( this ).text().trim() ) {
                $( this ).addClass( 'dark' );
            }
        } );

        $( '#tabs' ).tabs().show();
    } );
})( this, jQuery );
