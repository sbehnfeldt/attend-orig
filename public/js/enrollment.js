;(function ( global, $ ) {
    'use strict';


    // "Classroom" column on the Enrollment tab cannot be filled in until both the Students and Classrooms
    // data has been retrieved from the server.  This function checks to see whether this is true; if so,
    // draw the Enrollment table; it will be drawn with the names (rather than the IDs) of the classrooms
    // filled in
    function checkClassrooms() {
        if ( 0 === Classrooms.records.length ) {
            return;
        }
        if ( EnrollmentTab.isEmpty() ) {
            return;
        }
        EnrollmentTab.drawTable();
    }


    // The classrooms records from the database
    var Classrooms = (function () {
        var records   = {};
        var callbacks = {
            'classrooms-loaded': $.Callbacks()
        };

        function load( classrooms ) {
            for ( var i = 0; i < classrooms.length; i++ ) {
                var c          = classrooms[ i ];
                var idx        = c.Id;
                records[ idx ] = c;
            }
            callbacks[ 'classrooms-loaded' ].fire( records );
        }

        function subscribe( event, fn ) {
            callbacks[ event ].add( fn );
        }

        return {
            'records'  : records,
            'load'     : load,
            'subscribe': subscribe
        };
    })();


    // The schedules for each student
    var Schedules = (function () {
        var records = [];

        function load( schedules ) {
            for ( var i = 0; i < schedules.length; i++ ) {
                var s = schedules[ i ];
                if ( undefined === records[ s.StudentId ] ) {
                    records[ s.StudentId ] = [];
                }
                records[ s.StudentId ].push( s );
            }

            for ( var p in records ) {
                records[ p ].sort( function ( a, b ) {
                    if ( a.StartDate < b.StartDate ) return 1;
                    if ( a.StartDate > b.StartDate ) return -1;
                    return 0;
                } );
            }
        }

        function insert( s ) {
            if ( undefined === records[ s.StudentId ] ) {
                records[ s.StudentId ] = [];
            }
            records[ s.StudentId ].push( s );
            records[ s.StudentId ].sort( function ( a, b ) {
                if ( a.StartDate < b.StartDate ) return 1;
                if ( a.StartDate > b.StartDate ) return -1;
                return 0;
            } );
        }

        function update( s ) {
            for ( var i = 0; i < records[ s.StudentId ].length; i++ ) {
                if ( s.id === records[ s.StudentId ][ i ].id ) {
                    for ( var p in s ) {
                        records[ s.StudentId ][ i ][ p ] = s[ p ];
                    }
                    break;
                }
            }
        }


        return {
            'records': records,
            'load'   : load,
            'insert' : insert,
            'update' : update
        };
    })();


    //
    var EnrollmentTab = (function ( selector ) {
        var $self,
            table;

        $self = $( selector );
        table = $self.find( 'table.enrollment-table' ).DataTable( {
            "ajax"   : function () {
                Attend.loadAnother();
                $.ajax( {
                    'url'   : 'api/students',
                    'method': 'get',

                    'success': function ( json ) {
                        console.log( json );
                        for ( var i = 0; i < json.length; i++ ) {
                            table.row.add( json[ i ] );
                        }
                        table.draw();
                        Attend.doneLoading();
                    },
                    'error'  : function ( xhr ) {
                        console.log( xhr );
                        Attend.doneLoading();
                    }
                } );
            },
            "order"  : [ [ 1, 'asc' ] ],
            "select" : true,
            "columns": [
                { "data": "Id" },
                { "data": "FamilyName" },
                { "data": "FirstName" }, {
                    "data"  : "Enrolled",
                    "render": function ( data ) {
                        return '<input type=checkbox ' + (1 == data ? 'checked ' : '') + ' disabled />';
                    }
                }, {
                    "data"  : "ClassroomId",
                    "render": function ( data ) {
                        if ( data ) {
                            if ( Classrooms.records[ data ] ) {
                                return Classrooms.records[ data ].Label;
                            }
                            return data;
                        }
                        return '';
                    }
                }
            ],
            "initComplete": checkClassrooms
        } );

        var b0 = new $.fn.dataTable.Buttons( table, {
            buttons: [ {
                "text"  : "New",
                "action": function () {
                    StudentPropsDlg.open();
                }
            }, {
                "extend": "selected",
                "text"  : "Edit",
                "action": function ( e, dt, button, config ) {
                    var selected = dt.rows( { selected: true } ).indexes();
                    if ( 1 < selected.length ) {
                        alert( "Can edit only 1 record at a time" );
                    } else {
                        StudentPropsDlg.open( dt.rows( selected[ 0 ] ).data()[ 0 ] );
                    }
                }
            }, {
                "extend": "selected",
                "text"  : "Delete",
                "action": function ( e, dt ) {
                    var selected = dt.rows( { selected: true } );
                    var msg      = (1 === selected.length) ? 'Are you sure you want to delete this student record?' : 'Are you sure you want to delete these ' + selected.length + ' student records?';
                    if ( confirm( msg ) ) {
                        var length = selected[ 0 ].length;
                        selected.every( function () {
                            var row  = this;
                            var data = row.data();
                            console.log( data );
                            Attend.loadAnother();
                            $.ajax( {
                                "url"   : "api/students/" + data.Id,
                                "method": "delete",

                                "success": function ( json ) {
                                    length--;
                                    if ( !length ) {
                                        selected.remove().draw( false );
                                    }
                                    Attend.doneLoading();
                                },
                                "error"  : function () {
                                    console.log( xhr );
                                    length--;
                                    row.deselect();
                                    selected = dt.rows( { selected: true } );
                                    if ( !length ) {
                                        selected.remove().draw( false );
                                    }
                                    Attend.doneLoading();
                                }
                            } );
                        } );
                    }
                }
            } ]
        } );
        b0.dom.container.eq( 0 ).appendTo( $self.find( '.record-buttons' ) );

        var b1 = new $.fn.dataTable.Buttons( table, {
            "buttons": [ {
                "text"  : "Reload",
                "action": function ( e, dt ) {
                    table.clear();
                    Attend.loadAnother();
                    dt.ajax.reload( Attend.doneLoading );
                }
            } ]
        } );
        b1.dom.container.eq( 0 ).appendTo( $self.find( '.table-buttons span' ) );

        // Check whether there are any rows in the Enrollment table
        function isEmpty() {
            return ( 0 === table.rows().count() );
        }

        // Redraw the Enrollment table
        // This is used to convert classroom IDs in table to classroom names.
        function drawTable() {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                this.data( this.data() );   // Forces row to redraw
            } );
        }

        // Redraw a specific row in the Enrollment table
        function redrawRow( newData ) {
            table.rows().every( function ( /* rowIdx, tableLoop, rowLoop */ ) {
                var data = this.data();
                if ( data.Id == newData.Id ) {
                    this.data( newData );
                }
            } );
        }

        function insert( data ) {
            table.row.add( data ).draw();
        }

        function remove( studentId ) {
            console.log( studentId );
            table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                var data = this.data();
                if ( studentId == data.Id ) {
                    this.remove();
                }
            } );
            table.draw();
        }


        return {
            "isEmpty"  : isEmpty,
            "drawTable": drawTable,
            "redrawRow": redrawRow,
            "insert"   : insert
        };
    })( '#enrollment-tab' );


    var StudentPropsDlg = (function ( selector ) {
        var $self,
            $form,
            $studentId,
            $familyName,
            $firstName,
            $classrooms,
            $enrolled,
            $startDate,
            $list,

            $required,
            $buttons,
            $boxes,
            dialog;

        $self       = $( selector );
        $form       = $self.find( 'form' );
        $studentId  = $form.find( '[name=Id]' );
        $familyName = $self.find( '[name=FamilyName]' );
        $firstName  = $self.find( '[name=FirstName]' );
        $classrooms = $self.find( '[name=ClassroomsList]' );
        $enrolled   = $self.find( '[name=Enrolled]' );
        $startDate  = $self.find( '[name=WeekOf]' );
        $list       = $form.find( '[name=SchedulesList]' );

        $boxes    = $form.find( 'table.schedule-table input[type=checkbox]' );
        $buttons  = $form.find( 'table.schedule-table button' );
        $required = $form.find( '.required' );

        dialog = $self.dialog( {
            "autoOpen": false,
            "modal"   : true,
            "width"   : "450px",
            "buttons" : {
                "Submit": function onSubmit() {
                    if ( validate() ) {
                        submit();
                        close();
                    }
                },
                "Cancel": function onCancel() {
                    StudentPropsDlg.close();
                }
            }
        } );
        $startDate.datepicker( {
            'dateFormat': 'yy-mm-dd'
        } );

        // When a schedule button is clicked, Set/clear all the checkboxes in that button's row or column
        $( '.sched-button' ).on( 'click', function () {
            var key,   // Button's bit-map of checkbox values
                $b;    // Subset of checkboxes to set/clear

            key = $( this ).data( 'key' );

            // From the set of all checkboxes in the schedule table, select only those whose values are found
            // in the schedule button's bitmap key
            $b = $boxes.filter( function () {

                // Return true of the checkbox's value is turned on in the button's bitmap key
                return ( $( this ).val() & key );
            } );

            // If all of the checkboxes are already checked, clear them all; otherwise, set them all
            $b.prop( 'checked', $b.length !== $b.filter( ':checked' ).length );
        } );

        $buttons.on( 'click', function () {
            return false;
        } );

        $boxes.on( 'change', function () {
            // No op
        } );

        $list.on( 'change', function () {
            var id    = $studentId.val();
            var idx   = $( this )[ 0 ].selectedIndex;
            var sched = Schedules.records[ id ][ idx ].Schedule;
            $boxes.each( function ( idx, elem ) {
                if ( $( elem ).val() & sched ) {
                    $( elem ).prop( 'checked', true );
                } else {
                    $( elem ).prop( 'checked', false );
                }
            } );
        } );

        Classrooms.subscribe( 'classrooms-loaded', function ( classrooms ) {
            for ( var p in classrooms ) {
                var $option = $( '<option>' ).val( p ).text( classrooms[ p ].Label );
                $classrooms.append( $option );
            }
        } );

        // $classrooms is required only if student is enrolled
        $enrolled.on( 'click', function () {
            if ( $( this ).prop( 'checked' ) ) {
                $classrooms.addClass( 'required' );
            } else {
                $classrooms.removeClass( 'required' );
            }
        } );


        function open( student ) {
            clear();
            if ( student ) {
                populate( student );
            } else {
                $startDate.datepicker( 'setDate', Attend.getMonday( new Date() ) );
            }
            dialog.dialog( 'open' );
        }

        function close() {
            dialog.dialog( 'close' );
        }

        function clear() {
            $form[ 0 ].reset();
            $required.removeClass( 'missing' );
            $classrooms.addClass( 'required' );
            $list.empty();
            $list.addClass( 'hidden' );
        }

        function populate( student ) {
            var $opt;

            $studentId.val( student.Id );
            $familyName.val( student.FamilyName );
            $firstName.val( student.FirstName );
            $classrooms.val( student.ClassroomId );
            $enrolled.prop( 'checked', (1 == student.Enrolled) );
            $startDate.datepicker( 'setDate', Attend.getMonday( new Date() ) );

            $list.removeClass( 'hidden' );
            for ( var i = 0; i < Schedules.records[ student.Id ].length; i++ ) {
                var s = Schedules.records[ student.Id ][ i ];
                $opt  = $( '<option>' ).text( s.StartDate.split( 'T' )[ 0 ] ).val( s.Id );
                $list.append( $opt );
            }
            $list.trigger( 'change' );
        }

        function validate() {
            var valid = true;
            $required.each( function ( i, e ) {
                if ( !$( e ).val() ) {
                    $( e ).addClass( 'missing' );
                    valid = false;
                } else {
                    $( e ).removeClass( 'missing' );
                }
            } );
            return valid;
        }

        function submit() {
            var id,
                student,
                map;

            id      = $studentId.val();
            student = {
                "FamilyName" : $familyName.val(),
                "FirstName"  : $firstName.val(),
                "Enrolled"   : (true === $enrolled.prop( 'checked' )) ? 1 : 0,
                "ClassroomId": $classrooms.val() ? $classrooms.val() : null
            };

            map = 0;
            $boxes.each( function ( i, e ) {
                if ( $( e ).prop( 'checked' ) ) {
                    map += parseInt( $( e ).val(), 16 );
                }
            } );

            if ( !id ) {
                // Insert new student and schedule
                insert( student, {
                    StartDate: $startDate.val(),
                    Schedule : map
                } );

            } else {
                var idx = $list.prop( 'selectedIndex' );
                var cur = Schedules.records[ id ][ idx ];

                console.log( cur );
                if ( cur.schedule == map ) {
                    // Update student, leave schedule unchanged
                    update( id, student, null );
                } else {
                    // Update student, add new schedule
                    update( id, student, {
                        StartDate: $startDate.val(),
                        Schedule : map
                    } );
                }
            }
            StudentPropsDlg.close();
        }


        // Insert new student, new schedule
        function insert( student, schedule ) {
            Attend.loadAnother();
            $.ajax( {
                "url"   : "api/students",
                "method": "post",
                "data"  : student,

                "dataType": "json",
                "success" : function ( json ) {
                    console.log( json );
                    Attend.loadAnother();
                    $.ajax( {
                        'url'    : 'api/students/' + json.Id,
                        'method' : 'get',
                        'success': function ( json ) {
                            console.log( json );
                            EnrollmentTab.insert( json );
                            Attend.loadAnother();   // Get student record just loaded
                            schedule.StudentId = json.Id;
                            $.ajax( {
                                "url"   : "api/schedules",
                                "method": "post",
                                "data"  : schedule,

                                "dataType": "json",
                                "success" : function ( json ) {
                                    $.ajax( {
                                        'url'    : 'api/schedules/' + json.Id,
                                        'method' : 'get',
                                        'success': function ( json ) {
                                            console.log( json );
                                            Schedules.insert( json );
                                            Attend.doneLoading();
                                        },
                                        'error'  : function ( xhr ) {
                                            console.log( xhr );
                                            Attend.doneLoading();
                                        }
                                    } )
                                },
                                "error"   : function ( xhr ) {
                                    console.log( xhr );
                                    Attend.doneLoading();
                                    alert( "Error" );
                                }
                            } );
                            Attend.doneLoading();
                        },

                        'error': function ( xhr ) {
                            console.log( xhr );
                            Attend.doneLoading();
                        }
                    } );

                    Attend.doneLoading();
                },
                "error"   : function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
                    alert( "Error" );
                }
            } );
        }

        function update( id, student, schedule ) {
            Attend.loadAnother();
            $.ajax( {
                "url"   : "api/students/" + id,
                "method": "put",
                "data"  : student,

                "success": function ( json ) {
                    student.Id = id;

                    EnrollmentTab.redrawRow( student );
                    if ( schedule ) {
                        schedule.StudentId = id;

                        var d1 = $startDate.val();
                        for ( var i = 0; i < Schedules.records[ id ].length; i++ ) {
                            if ( d1 === Schedules.records[ id ][ i ].StartDate.split( 'T' )[ 0 ] ) {
                                break;
                            }
                        }
                        if ( i < Schedules.records[ id ].length ) {
                            // Update existing schedule
                            schedule.Id = Schedules.records[ id ][ i ].Id;
                            Attend.loadAnother();
                            $.ajax( {
                                "url"   : "api/schedules/" + schedule.Id,
                                "method": "put",
                                "data"  : schedule,

                                "dataType": "json",
                                "success" : function ( json ) {
                                    console.log( json );
                                    Schedules.update( schedule );
                                    Attend.doneLoading();
                                },
                                "error"   : function ( xhr ) {
                                    console.log( xhr );
                                    Attend.doneLoading();
                                }
                            } );
                        } else {
                            // Insert new schedule
                            Attend.loadAnother();
                            $.ajax( {
                                "url"   : "api/schedules",
                                "method": "post",
                                "data"  : schedule,

                                "dataType": "json",
                                "success" : function ( json ) {
                                    console.log( json );
                                    schedule.Id = json.Id;
                                    Schedules.insert( schedule );
                                    Attend.doneLoading();
                                },
                                "error"   : function ( xhr ) {
                                    console.log( xhr );
                                    Attend.doneLoading();
                                }
                            } );
                        }
                    }
                    Attend.doneLoading();
                },
                "error"  : function ( xhr, estring, e ) {
                    console.log( xhr );
                    console.log( estring );
                    console.log( e );
                    Attend.doneLoading();
                    alert( "Error" );
                }
            } );
        }


        return {
            'open' : open,
            'close': close
        };
    })( '#student-props-dlg' );


    $( function () {

        Attend.loadAnother();
        $.ajax( {
            'url'   : 'api/classrooms',
            'method': 'get',

            'dataType': 'json',
            'success' : function ( json ) {
                console.log( json );
                Classrooms.load( json );
                checkClassrooms();
                Attend.doneLoading();
            },
            'error'   : function ( xhr ) {
                console.log( xhr );
                Attend.doneLoading();
            }
        } );

        Attend.loadAnother();
        $.ajax( {
            'url'   : 'api/schedules',
            'method': 'get',

            'dataType': 'json',
            'success' : function ( json ) {
                console.log( json );
                Schedules.load( json );
                Attend.doneLoading();
            },
            'error'   : function ( xhr ) {
                console.log( "ERROR loading schedules" );
                console.log( xhr );
                Attend.doneLoading();
            }
        } )
    } );
    $( '#tabs' ).tabs().show();

})( this, jQuery );
