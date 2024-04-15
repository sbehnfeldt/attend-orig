;(function ( global, $ ) {
    'use strict';

    var loading  = 0;
    var $loading = $( '#loading' );

    global.Attend = global.Attend || {};

    Attend.loadAnother = function () {
        loading++;
        $loading.show();
    };

    Attend.doneLoading = function () {
        loading--;
        if ( 0 === loading ) {
            $loading.hide();
        }
    };


    Attend.getMonday = function ( d ) {

        var dw = d.getDay();
        switch ( dw ) {
            case 1:   // Monday: no op
                break;
            case 2:   // Tuesday:
                d.setDate( d.getDate() - 1 );
                break;
            case 3: // Wednesday:
                d.setDate( d.getDate() - 2 );
                break;
            case 4: // Thursday:
                d.setDate( d.getDate() - 3 );
                break;
            case 5:  // Friday:
                d.setDate( d.getDate() + 3 );
                break;
            case 6:
                d.setDate( d.getDate() + 2 );
                break;
            case 0:
                d.setDate( d.getDate() + 1 );
                break;
        }
        return d;
    };

    Date.prototype.addDays = function ( days ) {
        var date = new Date( this.valueOf() );
        date.setDate( date.getDate() + days );
        return date;
    };


})(this, jQuery);

