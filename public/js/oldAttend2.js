;(function () {
    'use strict';

    // Model, container of individual records
    var Records = (function () {
        function init() {
            this.records   = {};
            this.callbacks = {
                'empty-records': $.Callbacks(),
                'load-records' : $.Callbacks(),
                'insert-record': $.Callbacks(),
                'remove-record': $.Callbacks(),
                'update-record': $.Callbacks()
            }
        }

        function subscribe( event, fn ) {
            this.callbacks[ event ].add( fn );
            return this;
        }

        function empty() {
            this.records = {};
            this.callbacks[ 'empty-records' ].fire();
        }

        function load( records ) {
            for ( var i = 0; i < records.length; i++ ) {
                var r                            = records[ i ];
                this.records[ parseInt( r.id ) ] = r;   // TODO: Why parse the ID? Won't the ID work as a string?
            }
            this.callbacks[ 'load-records' ].fire( records );
            return this;
        }

        function insert( record ) {
            this.records[ parseInt( record.id ) ] = record;
            this.callbacks[ 'insert-record' ].fire( record.id );
            return this;
        }

        function update( id, updates ) {
            console.log( updates );
            for ( var p in updates ) {
                this.records[ parseInt( id ) ][ p ] = updates[ p ];
            }
            this.callbacks[ 'update-record' ].fire( id, updates );
            return this;
        }

        function remove( id ) {
            this.records[ parseInt( id ) ] = undefined;
            this.callbacks[ 'remove-record' ].fire( id );
            return this;
        }

        return {
            'init'     : init,
            'subscribe': subscribe,
            'empty'    : empty,
            'load'     : load,
            'insert'   : insert,
            'update'   : update,
            'remove'   : remove
        };

    })();

    var Classrooms = Object.create( Records );
    var Students   = Object.create( Records );
    var Schedules  = Object.create( Records );


    Students.init = function () {
        this.records   = {};
        this.callbacks = {
            'empty-records': $.Callbacks(),
            'load-records' : $.Callbacks(),
            'insert-record': $.Callbacks(),
            'remove-record': $.Callbacks(),
            'update-record': $.Callbacks()
        };

        // object mapping classroom ID to list of associated student IDs
        this.classrooms = {};
    };

    Students.load = function ( records ) {
        for ( var i = 0; i < records.length; i++ ) {
            var student = records[ i ];


            // Store student in records object by student ID
            this.records[ parseInt( student.id ) ] = student;


            var classroomId = parseInt( student.classroom_id );
            if ( !( classroomId in this.classrooms) ) {
                this.classrooms[ classroomId ] = [];
            }
            this.classrooms[ classroomId ].push( student.id );
        }

        // Now that all students are loaded, sort the classrooms by student name (NOT id)
        for ( classroomId in this.classrooms ) {
            this.classrooms[ classroomId ].sort( function ( id1, id2 ) {
                var student1 = Students.records[ id1 ];
                var student2 = Students.records[ id2 ];
                if ( student1.family_name < student2.family_name ) return -1;
                if ( student1.family_name > student2.family_name ) return 1;
                if ( student1.first_name < student2.first_name ) return -1;
                if ( student1.first_name > student2.first_name ) return 1;
                return 0;
            } );
        }

        this.callbacks[ 'load-records' ].fire( records );
        return this;
    };

    Students.insert = function ( record ) {
        this.records[ parseInt( record.id ) ] = record;

        var classroomId = parseInt( record.classroom_id );
        if ( !( classroomId in this.classrooms) ) {
            this.classrooms[ classroomId ] = [];
        }
        this.classrooms[ classroomId ].push( record.id );
        this.classrooms[ classroomId ].sort( function ( id1, id2 ) {
            var student1 = Students.records[ id1 ];
            var student2 = Students.records[ id2 ];
            if ( student1.family_name < student2.family_name ) return -1;
            if ( student1.family_name > student2.family_name ) return 1;
            if ( student1.first_name < student2.first_name ) return -1;
            if ( student1.first_name > student2.first_name ) return 1;
            return 0;
        } );

        this.callbacks[ 'insert-record' ].fire( record.id );
        return this;
    };

    Students.update = function ( id, updates ) {
        var student = this.records[ id ];
        if ( ('classroom_id' in updates) && (updates.classroom_id != student.classroom_id) ) {

            // Student classroom has been changed.  Remove student from old classroom
            var idx = this.classrooms[ parseInt( student.classroom_id ) ].indexOf( id );
            if ( undefined != idx ) {
                this.classrooms[ student.classroom_id ].splice( idx, 1 );
            }

            // Push student to new classroom
            this.classrooms[ updates.classroom_id ].push( id );
            this.classrooms[ updates.classroom_id ].sort( function ( id1, id2 ) {
                var student1 = Students.records[ id1 ];
                var student2 = Students.records[ id2 ];
                if ( student1.family_name < student2.family_name ) return -1;
                if ( student1.family_name > student2.family_name ) return 1;
                if ( student1.first_name < student2.first_name ) return -1;
                if ( student1.first_name > student2.first_name ) return 1;
                return 0;
            } );
        }
        for ( var p in updates ) {
            this.records[ id ][ p ] = updates[ p ];
        }
        this.callbacks[ 'update-record' ].fire( id, updates );
        return this;
    };

    Students.remove = function ( id ) {
        var student = Students.records[ id ];
        var idx     = this.classrooms[ parseInt( student.classroom_id ) ].indexOf( id );
        if ( undefined != idx ) {
            this.classrooms[ student.classroom_id ].splice( idx, 1 );
        }

        this.records[ parseInt( id ) ] = undefined;
        this.callbacks[ 'remove-record' ].fire( id );
        return this;
    };


    /**
     * Apart from being stored by ID, schedules must at the same time be grouped by students, for easier access later
     * on. Therefore, many of the methods of the base object "Records" must be overridden.
     */
    Schedules.init = function () {
        this.records   = {};
        this.callbacks = {
            'empty-records': $.Callbacks(),
            'load-records' : $.Callbacks(),
            'insert-record': $.Callbacks(),
            'remove-record': $.Callbacks(),
            'update-record': $.Callbacks()
        };
        this.students  = {};
    };

    Schedules.load = function ( records ) {
        for ( var i = 0; i < records.length; i++ ) {
            var schedule = records[ i ];

            this.records[ parseInt( schedule.id ) ] = schedule;

            var studentId = parseInt( schedule.student_id );
            if ( !( studentId in this.students ) ) {
                this.students[ studentId ] = [];
            }
            this.students[ studentId ].push( schedule );
        }
        for ( studentId in this.students ) {
            var arr = this.students[ studentId ];
            arr.sort( function ( a, b ) {
                if ( a.start_date > b.start_date ) return 1;
                if ( a.start_date < b.start_date ) return -1;
                return 0;
            } );
        }
        this.callbacks[ 'load-records' ].fire( records );
        return this;
    };

    Schedules.insert = function ( schedule ) {
        this.records[ parseInt( schedule.id ) ] = schedule;

        var studentId = parseInt( schedule.student_id );
        if ( !( studentId in this.students ) ) {
            this.students[ studentId ] = [];
        }
        this.students[ studentId ].push( schedule );
        this.students[ studentId ].sort( function ( a, b ) {
            if ( a.start_date > b.start_date ) return 1;
            if ( a.start_date < b.start_date ) return -1;
            return 0;
        } );
        this.callbacks[ 'insert-record' ].fire( schedule.id );
        return this;
    };

    // Communicator between front and back ends
    function Uhura( url, model ) {
        this.url   = url;
        this.model = model;

        this.load   = function () {
            var self = this;
            $.ajax( {
                'url'   : this.url,
                'method': 'get',

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    self.model.load( json.data )
                },
                'error'   : function ( xhr ) {
                    console.log( xhr );
                    if ( xhr.responseJSON ) {
                        alert( xhr.responseJSON.message );
                    } else {
                        alert( "Unhandled error" );
                    }
                }
            } );
        };
        this.insert = function ( data ) {
            var self = this;
            $.ajax( {
                'url'   : this.url,
                'method': 'post',
                'data'  : $.param( data ),

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    self.model.insert( json.resource )
                },
                'error'   : function ( xhr ) {
                    console.log( xhr );
                    if ( xhr.responseJSON ) {
                        alert( xhr.responseJSON.message );
                    } else {
                        alert( "Unhandled error" );
                    }
                }
            } );
        };
        this.update = function ( id, params ) {
            var self = this;
            $.ajax( {
                'url'   : this.url + '/' + id,
                'method': 'put',
                'data'  : $.param( params ),

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    self.model.update( id, params )
                },
                'error'   : function ( xhr ) {
                    console.log( xhr );
                    if ( xhr.responseJSON ) {
                        alert( xhr.responseJSON.message );
                    } else {
                        alert( "Unhandled error" );
                    }
                }
            } );
        };
        this.remove = function ( id ) {
            var self = this;
            $.ajax( {
                'url'   : this.url + '/' + id,
                'method': 'delete',

                'dataType': 'json',
                'success' : function ( json ) {
                    console.log( json );
                    self.model.remove( id );
                },
                'error'   : function ( xhr ) {
                    console.log( xhr );
                    if ( xhr.responseJSON ) {
                        alert( xhr.responseJSON.message );
                    } else {
                        alert( "Unhandled error" );
                    }
                }
            } );
        };
    }

    var ClassroomController = new Uhura( '/attend-api/classrooms', Classrooms );
    var StudentController   = new Uhura( '/attend-api/students', Students );
    var SchedulesController = new Uhura( '/attend-api/schedules', Schedules );


    /****************************************************************************************************
     * Classrooms Table
     * Table of classrooms on the "Classrooms" page
     ****************************************************************************************************/
    var ClassroomsTable = (function () {
        var $table;
        var table;

        function init( selector ) {
            $table = $( selector );
            table  = $table.DataTable( {
                'info'     : false,
                'paging'   : false,
                'searching': false,
                'ordering' : false
            } );
            $table.on( 'click', 'button.edit', onClickUpdateClassroom );
            $table.on( 'click', 'button.delete', onClickDeleteClassroom );

            Classrooms.subscribe( 'empty-records', whenClassroomsEmptied );
            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );
            Classrooms.subscribe( 'insert-record', whenClassroomAdded );
            Classrooms.subscribe( 'update-record', whenClassroomUpdated );
            Classrooms.subscribe( 'remove-record', whenClassroomRemoved );
        }

        function toArray( classroom ) {
            return [
                classroom && classroom.label ? classroom.label : '',
                '<button class="edit"><span class="glyphicon glyphicon-edit" /> </button>&nbsp;',
                '<button class="delete"><span class="glyphicon glyphicon-remove" /> </button>'
            ];
        }

        // Add a new row to the classroom table
        function addClassroom( classroom ) {
            var row = table.row.add( toArray( classroom ) );
            $( row.node() ).data( 'classroomId', classroom ? classroom.id : '' );
            return row;
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handlers
        ////////////////////////////////////////////////////////////////////////////////

        // When the 'Update' button for an existing classroom is clicked,
        // open the Classroom Properties dialog
        function onClickUpdateClassroom() {
            var $tr         = $( this ).closest( 'tr' );
            var classroomId = $tr.data( 'classroomId' );
            ClassroomPropsDlg.open( Classrooms.records[ classroomId ] );
        }

        // When the 'Delete' button for an existing classroom is clicked,
        // delete the data from the database (via the controller)
        function onClickDeleteClassroom() {
            var $tr = $( this ).closest( 'tr' );
            var id  = $tr.data( 'classroomId' );
            if ( window.confirm( 'Are you sure you want to delete the ' + Classrooms.records[ id ].name + ' classroom?' ) ) {
                ClassroomController.remove( id );
            }
        }

        ////////////////////////////////////////////////////////////////////////////////
        // External Event Callbacks
        ////////////////////////////////////////////////////////////////////////////////

        // WHen classroom model is cleared of all records,
        // empty the Classrooms table
        function whenClassroomsEmptied() {
            table.clear().draw();
        }

        // When classrooms are loaded into the model,
        // populate the Classrooms table
        function whenClassroomsLoaded( records ) {
            for ( var id in records ) {
                addClassroom( records[ id ] );
            }
            table.draw();
        }

        // When a new classroom is added to the model,
        // add a new classroom to the Classrooms table
        function whenClassroomAdded( classroom ) {
            var row = table.row( '.new-classroom' );
            row.remove();
            $( row ).remove();

            table.addClassroom( classroom );
            table.draw();
            $newButton.show();
        }

        // When a classroom is updated in the model,
        // update the corresponding row in the table acordingly
        function whenClassroomUpdated( id, updates ) {
            var rows = table.rows().nodes();
            rows.each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'classroomId' );
                if ( data === id ) {
                    var row = table.row( $tr );
                    row.data( toArray( Classrooms.records[ id ] ) );
                    $( row.node() ).data( 'classroomId', id );
                    row.draw();
                    return false;
                }
            } );
        }

        // When a classroom is removed from the model,
        // remove the corresponding row from the Classrooms table
        function whenClassroomRemoved( id ) {
            table.rows().nodes().each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'classroomId' );
                if ( data === id ) {
                    $tr.remove();
                    table.row( e ).remove();
                    return false;
                }
            } );
        }


        return {
            'init': init
        };
    })();

    /****************************************************************************************************
     * Classroom Properties Dialog
     * Dialog box for setting the properties of a new or existing classroom
     ****************************************************************************************************/
    var ClassroomPropsDlg = (function () {
        var $dialog;
        var $form;
        var $id;
        var $tips;
        var $label;
        var $allFields;

        var dialog;
        var tipsTimer;

        function init( selector ) {
            $dialog    = $( selector );
            $form      = $dialog.find( 'form' );
            $id        = $form.find( 'input[name=id]' );
            $tips      = $form.find( 'p.update-tips' );
            $label     = $form.find( 'input[name=label]' );
            $allFields = $form.find( 'input' );

            dialog    = $dialog.dialog( {
                autoOpen: false,
                modal   : true,
                buttons : {
                    "Submit": onClickSubmitClassroomForm,
                    "Cancel": function () {
                        var $modified = $dialog.find( '.modified' );
                        if ( $modified.length ) {
                            if ( confirm( "Are you sure you want to discard your changes?" ) ) {
                                dialog.dialog( 'close' );
                            }
                        } else {
                            dialog.dialog( "close" );
                        }
                    }
                },
                "close" : clear
            } );
            tipsTimer = null;

            $allFields.on( 'change', onChangeAllFields );
        }

        function clear() {
            $form[ 0 ].reset();
            $tips
                    .text( '' )
                    .removeClass( "ui-state-highlight" );
            if ( tipsTimer ) {
                clearTimeout( tipsTimer );
                tipsTimer = null;
            }
            $( '.modified' ).removeClass( 'modified' );
            $( '.ui-state-error' ).removeClass( 'ui-state-error' );
        }

        function populate( classroom ) {
            $id.val( classroom.id );
            $label.val( classroom.label );
            $label.data( 'db-val', classroom.label );
        }

        function open( classroom ) {
            clear();
            if ( classroom ) {
                populate( classroom );
            }

            dialog.dialog( 'open' );
        }

        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be between " +
                        min + " and " + max + "." );
                return false;
            } else {
                return true;
            }
        }

        function updateTips( t ) {
            $tips
                    .text( t )
                    .addClass( "ui-state-highlight" );
            tipsTimer = setTimeout( function () {
                $tips.removeClass( "ui-state-highlight", 1500 );
            }, 2500 );
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handlers
        ////////////////////////////////////////////////////////////////////////////////

        // When the 'Submit' button on the classroom dialog form is clicked,
        // enter the new classroom data into the database (via the controller)
        function onClickSubmitClassroomForm() {
            $tips.text( '' ).removeClass( 'ui-state-highlight' );
            var valid = true;
            valid     = valid && checkLength( $label, "label", 1, 55 );

            if ( valid ) {
                if ( $id.val() ) {
                    ClassroomController.update( $id.val(), {
                        'label': $label.val()
                    } );
                } else {
                    ClassroomController.insert( {
                        'label': $label.val()
                    } );
                }
                dialog.dialog( "close" );
            }
            return valid;
        }

        function onChangeAllFields() {
            if ( $( this ).val() != $( this ).data( 'db-val' ) ) {
                $( this ).addClass( 'modified' );
            } else {
                $( this ).removeClass( 'modified' );
            }
        }


        return {
            'init': init,
            'open': open
        };
    })();


    /****************************************************************************************************
     * Classrooms Page
     * Page containing the Classrooms table.
     ****************************************************************************************************/
    var ClassroomsPage = (function () {
        var $panel;
        var $newButton;
        var $refreshButton;

        function init( selector ) {
            $panel         = $( selector );
            $newButton     = $panel.find( 'button.new-record' );
            $refreshButton = $panel.find( 'button.refresh-records' );
            $newButton.on( 'click', onClickNewClassroom );
            $refreshButton.on( 'click', onRefreshClassrooms );
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////


        // When the "New Classroom" button is clicked,
        // add an empty row to the end of the Classrooms table
        function onClickNewClassroom() {
            ClassroomPropsDlg.open();
        }

        function onRefreshClassrooms() {
            Classrooms.empty();
            ClassroomController.load();
        }

        return {
            'init': init
        }
    })();


    /****************************************************************************************************
     * Enrollment Table
     * Table of students on the "Enrollment" page
     ****************************************************************************************************/
    var EnrollmentTable = (function () {

        var $table;
        var table;

        function init( selector ) {
            $table = $( selector );
            table  = $table.DataTable( {} );
            $table.on( 'click', 'button.edit', onClickEditStudent );
            $table.on( 'click', 'button.schedules', onClickEditSchedules );
            $table.on( 'click', 'button.delete', onClickDeleteStudent );

            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );

            Students.subscribe( 'empty-records', whenStudentsEmptied );
            Students.subscribe( 'load-records', whenStudentsLoaded );
            Students.subscribe( 'remove-record', whenStudentRemoved );
            Students.subscribe( 'insert-record', whenStudentAdded );
            Students.subscribe( 'update-record', whenStudentUpdated );
        }

        function toArray( student ) {
            return [
                student.family_name,
                student.first_name,
                (student.classroom_id in Classrooms.records)
                        ? '<span class="classroom">' + Classrooms.records[ student.classroom_id ].label + '</span>'
                        : '<span class="classroom">' + student.classroom_id + '</span>',

                '<input type="checkbox" name="enrolled" disabled ' + (( "1" == student.enrolled ) ? ' checked' : '') + '/>',
                '<button class="edit"><span class="glyphicon glyphicon-edit" /> </button>',
                '<button class="schedules"><span class="glyphicon glyphicon-time" /> </button>',
                '<button class="delete"><span class="glyphicon glyphicon-remove" /> </button>'
            ];
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////

        function onClickEditStudent() {
            var $tr = $( this ).closest( 'tr' );
            var id  = $tr.data( 'studentId' );
            StudentPropsDlg.open( Students.records[ id ] );
        }

        function onClickEditSchedules() {
            var $tr = $( this ).closest( 'tr' );
            var id  = $tr.data( 'studentId' );
            ScheduleDlg.open( id );
        }

        function onClickDeleteStudent() {
            var $tr, id, student;
            $tr     = $( this ).closest( 'tr' );
            id      = $tr.data( 'studentId' );
            student = Students.records[ id ];
            console.log( student );
            if ( confirm( 'Are you sure you want to DELETE ' + student.first_name + ' ' + student.family_name + ' from the database?' ) ) {
                StudentController.remove( id );
            }
        }

        ////////////////////////////////////////////////////////////////////////////////
        // External Event Callback Functions
        ////////////////////////////////////////////////////////////////////////////////

        function whenStudentsEmptied() {
            table.clear().draw();
        }

        function whenStudentsLoaded( records ) {
            for ( var id in records ) {
                var row = table.row.add( toArray( records[ id ] ) );
                $( row.node() ).data( 'studentId', records[ id ].id );
            }
            table.draw();
        }

        function whenStudentAdded( studentId ) {
            var row = table.row.add( toArray( Students.records[ studentId ] ) );
            $( row.node() ).data( 'studentId', Students.records[ studentId ].id );
            row.draw();

            ScheduleDlg.open( studentId );
        }

        function whenStudentUpdated( id, updates ) {
            var rows = table.rows().nodes();
            rows.each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'studentId' );
                if ( data === id ) {
                    var row = table.row( $tr );
                    row.data( toArray( Students.records[ id ] ) );
                    $( row.node() ).data( 'studentId', id );
                    row.draw();
                    return false;
                }
            } );

        }

        function whenStudentRemoved( id ) {
            table.rows().nodes().each( function ( e, i ) {
                var $tr  = $( e );
                var data = $tr.data( 'studentId' );
                if ( data === id ) {
                    table.row( $tr ).remove();
                    return false;
                }
            } );
            table.draw();
        }

        // When the classrooms are loaded,
        // replace the classroom ID in the Students table with the corresponding classroom name
        function whenClassroomsLoaded() {
            table.rows().nodes().each( function ( tr, i, a ) {
                var studentId   = $( tr ).data( 'studentId' );
                var classroomId = Students.records[ studentId ].classroom_id;
                $( tr ).find( 'span.classroom' ).text(
                        (classroomId in Classrooms.records) ?
                                Classrooms.records[ classroomId ].label :
                                Students.records[ studentId ].classroom_id );
            } );
        }

        return {
            'init': init
        };
    })();


    /****************************************************************************************************
     * Student Properties Dialog
     * Dialog box for setting the properties of a new or existing student
     ****************************************************************************************************/
    var StudentPropsDlg = (function () {
        var $dialog;

        var $form;
        var $id;
        var $tips;
        var $familyName;
        var $firstName;
        var $classrooms;
        var $active;
        var $allFields;

        var dialog;
        var tipsTimer;

        function init( selector ) {
            $dialog     = $( selector );
            $form       = $dialog.find( 'form[name=studentData]' );
            $id         = $dialog.find( 'input[name=id]' );
            $tips       = $dialog.find( 'p.update-tips' );
            $familyName = $form.find( 'input[name=family_name]' );
            $firstName  = $form.find( 'input[name=first_name]' );
            $classrooms = $form.find( 'select[name=classroom_id]' );
            for ( var p in Classrooms.records ) {
                var $opt = $( 'option' ).text( p ).val( p );
                $classrooms.append( $opt );
            }
            $active    = $dialog.find( 'input[name=enrolled]' );
            $allFields = $form.find( 'input' ).add( $form.find( 'select' ) );

            dialog = $dialog.dialog( {
                autoOpen: false,
                modal   : true,
                width   : '50%',
                buttons : {
                    "Submit": onClickSubmiStudentForm,
                    "Cancel": function () {
                        var $modified = $dialog.find( '.modified' );
                        if ( $modified.length ) {
                            if ( confirm( "Are you sure you want to discard your changes?" ) ) {
                                dialog.dialog( 'close' );
                            }
                        } else {
                            dialog.dialog( "close" );
                        }
                    }
                },
                "close" : clear
            } );

            tipsTimer = null;

            $allFields.on( 'change', onChangeAllFields );

            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );
            Classrooms.subscribe( 'insert-record', whenClassroomAdded );
            Classrooms.subscribe( 'update-record', whenClassroomUpdated );
        }

        function clear() {
            $form[ 0 ].reset();
            $tips
                    .text( '' )
                    .removeClass( "ui-state-highlight" );
            if ( tipsTimer ) {
                clearTimeout( tipsTimer );
                tipsTimer = null;
            }
            $( '.modified' ).removeClass( 'modified' );
            $( '.ui-state-error' ).removeClass( 'ui-state-error' );
        }

        function open( student ) {
            if ( student ) {
                $id.val( student.id );
                $familyName.val( student.family_name );
                $firstName.val( student.first_name );
                $classrooms.val( student.classroom_id );
                $active.prop( 'checked', (student.enrolled === "1") );
            }

            dialog.dialog( 'open' );
        }

        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be between " +
                        min + " and " + max + "." );
                return false;
            }
            o.removeClass( "ui-state-error" );
            return true;
        }

        function checkSelected( o, n ) {
            if ( !parseInt( o.val() ) ) {
                o.addClass( 'ui-state-error' );
                updateTips( "Selection is required from " + n );
                return false;
            }
            o.removeClass( 'ui-state-error' );
            return true;
        }

        function updateTips( t ) {
            $tips
                    .text( t )
                    .addClass( "ui-state-highlight" );
            tipsTimer = setTimeout( function () {
                $tips.removeClass( "ui-state-highlight", 1500 );
            }, 2500 );
        }

        function submit() {
            $tips.text( '' ).removeClass( 'ui-state-highlight' );
            if ( tipsTimer ) {
                clearTimeout( tipsTimer );
                tipsTimer = null;
            }
            var valid = true;
            valid     = valid && checkLength( $familyName, "family name", 1, 55 );
            valid     = valid && checkLength( $firstName, "first name", 1, 55 );
            valid     = valid && checkSelected( $classrooms, "classroom" );
            if ( valid ) {
                if ( $id.val() ) {
                    StudentController.update( $id.val(), {
                        'family_name' : $familyName.val(),
                        'first_name'  : $firstName.val(),
                        'enrolled'    : $active.is( ':checked' ) ? "1" : "0",
                        'classroom_id': $classrooms.val()
                    } );
                } else {
                    StudentController.insert( {
                        'family_name' : $familyName.val(),
                        'first_name'  : $firstName.val(),
                        'enrolled'    : $active.is( ':checked' ) ? "1" : "0",
                        'classroom_id': $classrooms.val()
                    } );
                }
            }
            return valid;
        }

        ////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////

        function onClickSubmiStudentForm() {
            if ( submit() ) {
                dialog.dialog( "close" );
            }
        }

        function onChangeAllFields() {
            if ( $( this ).val() != $( this ).data( 'db-val' ) ) {
                $( this ).addClass( 'modified' );
            } else {
                $( this ).removeClass( 'modified' );
            }
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        // External Event Callbacks
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        function whenClassroomsLoaded( classrooms ) {
            var $opt;
            for ( var i in classrooms ) {
                var classroom = classrooms[ i ];
                $opt          = $( '<option>' ).text( classroom.label ).val( classroom.id );
                $classrooms.append( $opt );
            }
        }

        function whenClassroomAdded( classroom ) {
        }

        function whenClassroomUpdated( classroomId, updates ) {
        }


        return {
            'init': init,
            'open': open
        };
    })();


    /****************************************************************************************************
     * Student Schedule Dialog
     * Dialog box for setting a student's schedule
     ****************************************************************************************************/
    var ScheduleDlg = (function () {
        var $dialog;
        var $studentName;
        var $tips;
        var $form;
        var $studentId;   // Value
        var $schedList;   // Value: Drop-down list of schedules
        var $scheds;      // Value: Checkboxes
        var $checkAll;    // Control: select/de-select all checkboxes at once
        var $schedGroups; // Control: select/de-select row/column of checkboxes at once
        var $startDate;   // Value

        var dialog;
        var tipsTimer;


        function init( selector ) {
            $dialog      = $( selector );
            $studentName = $dialog.find( 'p.student-name' );
            $tips        = $dialog.find( 'p.update-tips' );
            $form        = $dialog.find( 'form[name=schedules]' );
            $studentId   = $form.find( 'input[name=student_id]' );
            $schedList   = $form.find( 'select[name=id]' );
            $scheds      = $form.find( 'input.scheds' );
            $checkAll    = $form.find( 'button.checkAll' );
            $schedGroups = $form.find( 'button.sched-group' );
            $startDate   = $form.find( 'input[name=start_date]' );

            $startDate.datepicker( {
                dateFormat: 'yy-mm-dd'
            } );

            dialog    = $dialog.dialog( {
                autoOpen: false,
                modal   : true,
                width   : '50%',
                buttons : {
                    "Submit": onClickSubmitSchedule,
                    "Cancel": function () {
                        var $modified = $dialog.find( '.modified' );
                        if ( $modified.length ) {
                            if ( confirm( "Are you sure you want to discard your changes?" ) ) {
                                dialog.dialog( 'close' );
                            }
                        } else {
                            dialog.dialog( "close" );
                        }
                    }
                },
                "close" : clear
            } );
            tipsTimer = null;

            $schedList.on( 'change', onChangeSchedList );
            $scheds.on( 'click', onClickScheds );
            $checkAll.on( 'click', onClickCheckAll );
            $schedGroups.on( 'click', onClickSchedGroup );
        }

        function clear() {
            $form[ 0 ].reset();
            $schedList.empty();
            $tips
                    .text( '' )
                    .removeClass( "ui-state-highlight" );
            if ( tipsTimer ) {
                clearTimeout( tipsTimer );
                tipsTimer = null;
            }
            $( '.modified' ).removeClass( 'modified' );
            $( '.ui-state-error' ).removeClass( 'ui-state-error' );
            $startDate.attr( 'disabled', true );
        }

        function populate( studentId ) {
            $studentName.text( Students.records[ studentId ].first_name + ' ' + Students.records[ studentId ].family_name );
            $studentId.val( studentId );

            var scheds = [];
            for ( var p in Schedules.records ) {
                if ( studentId === Schedules.records[ p ].student_id ) {
                    scheds.push( p );
                }
            }

            if ( scheds.length ) {
                scheds.sort( function ( a, b ) {
                    if ( Schedules.records[ a ].id < Schedules.records[ b ].id ) return 1;
                    if ( Schedules.records[ a ].id > Schedules.records[ b ].id ) return -1;
                    return 0;
                } );
                for ( var i = 0; i < scheds.length; i++ ) {
                    var $opt = $( '<option>' ).val( scheds[ i ] ).text( Schedules.records[ scheds[ i ] ].start_date );
                    $schedList.append( $opt );
                }
            }
            $schedList.trigger( 'change' );   // Act as though we just picked date from drop-down; so to populate schedule table
        }

        function open( studentId ) {
            if ( studentId ) {
                populate( studentId );
            }
            dialog.dialog( 'open' );
        }

        function validate() {
            return true;
        }

        function checkModified() {
            var $modified = $form.find( '.modified' );
            if ( $modified.length > 0 ) {
                var d  = new Date();
                var yy = 1900 + d.getYear();
                var mm = d.getMonth() + 1;
                if ( mm < 10 ) mm = '0' + mm;
                var dd = d.getDate();

                $startDate.attr( 'disabled', false );
                $startDate.val( yy + '-' + mm + '-' + dd );
            } else {
                $startDate.attr( 'disabled', true );
                $startDate.val( '' );
            }
        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        // Internal Event Handlers
        ////////////////////////////////////////////////////////////////////////////////////////////////////

        // When a new schedule is selected, update the schedule table
        function onChangeSchedList() {
            var $list = $( this );
            if ( $list.val() ) {
                $scheds.each( function ( i, e ) {
                    $( e ).prop( 'checked', ($( e ).val() & Schedules.records[ $list.val() ].schedule) );
                    $( e ).data( 'data', ($( e ).val() & Schedules.records[ $list.val() ].schedule) );
                } );
            } else {
                $scheds.each( function ( i, e ) {
                    $( e ).prop( 'checked', false );
                    $( e ).data( 'data', 0 );
                } );
            }

        }


        // When one of the scheduling check boxes in the scheduling dialog is clicked,
        // reverse its state.  If no longer in its original state, add "modified" class to parent.
        function onClickScheds() {
            if ( $( this ).is( ':checked' ) ) {
                if ( $( this ).data( 'data' ) ) {
                    $( this ).parent().removeClass( 'modified' );
                } else {
                    $( this ).parent().addClass( 'modified' );
                }
            } else {
                if ( $( this ).data( 'data' ) ) {
                    $( this ).parent().addClass( 'modified' );
                } else {
                    $( this ).parent().removeClass( 'modified' );
                }
            }
            checkModified();
        }

        // Programmatically 'click' all the scheduling checkboxes in the corresponding row or column
        function onClickSchedGroup() {
            var self = this;
            $scheds.each( function () {
                if ( $( self ).val() & $( this ).val() ) {
                    $( this ).trigger( 'click' );
                }
            } );
            checkModified();
        }

        function onClickCheckAll() {
            if ( $scheds.length !== $scheds.filter( ':checked' ).length ) {
                console.log( "Click all" );
                $scheds.prop( 'checked', true );
            } else {
                console.log( "Clear all" );
                $scheds.prop( 'checked', false );
            }
            $scheds.each( function () {
                if ( $( this ).is( ':checked' ) ) {
                    if ( $( this ).data( 'data' ) ) {
                        $( this ).parent().removeClass( 'modified' );
                    } else {
                        $( this ).parent().addClass( 'modified' );
                    }
                } else {
                    if ( $( this ).data( 'data' ) ) {
                        $( this ).parent().addClass( 'modified' );
                    } else {
                        $( this ).parent().removeClass( 'modified' );
                    }
                }
            } );
            checkModified();
        }


        function onClickSubmitSchedule() {
            if ( validate() ) {
                var schedule = 0;
                $scheds.each( function ( i, e ) {
                    if ( $( e ).is( ':checked' ) ) {
                        schedule |= $( e ).val();
                    }
                } );
                if ( $schedList.val() && ( $startDate.val() == $schedList.find( ':selected' ).text()) ) {
                    SchedulesController.update( $schedList.val(), {
                        'student_id': $studentId.val(),
                        'schedule'  : schedule,
                        'start_date': $startDate.val()
                    } );
                } else {
                    SchedulesController.insert( {
                        'student_id': $studentId.val(),
                        'schedule'  : schedule,
                        'start_date': $startDate.val()
                    } );
                }
                dialog.dialog( 'close' );
            }
        }


        return {
            'init': init,
            'open': open
        }

    })();


    /****************************************************************************************************
     * Enrollment Page
     * Page containing the list of students
     ****************************************************************************************************/
    var EnrollmentPage = (function () {
        var $panel;
        var $newButton;
        var $refreshButton;

        function init( selector ) {
            $panel         = $( selector );
            $newButton     = $panel.find( 'button.new-record' );
            $refreshButton = $panel.find( 'button.refresh-records' );

            $newButton.on( 'click', onClickNewStudent );
            $refreshButton.on( 'click', onClickRefreshStudents );
        }


        ////////////////////////////////////////////////////////////////////////////////
        // Event Handler Functions
        ////////////////////////////////////////////////////////////////////////////////
        function onClickNewStudent() {
            StudentPropsDlg.open();
        }

        function onClickRefreshStudents() {
            Students.empty();
            StudentController.load();
        }


        return {
            'init': init
        };
    })();


    // Return a Date object set to Monday of the week of the input date.
    function normalizeDateToMonday( date ) {
        if ( false === (date instanceof Date) ) {
            throw 'Can only normalize a Date object';
        }
        if ( date.getDay() < 6 ) {
            // normalize to Monday of this week
            date = new Date( date.getFullYear(), date.getMonth(), date.getDate() - (date.getDay() - 1) );
        } else {
            // Normalize to Monday of next week
            date = new Date( date.getFullYear(), date.getMonth(), date.getDate() + 2 );
        }
        return date;
    }


    /********************************************************************************
     * Page showing attendance sheets for the week
     * Includes a link to generate attendance sheets in PDF form
     ********************************************************************************/
    var AttendancePage = (function () {
        var $page,
            $schedTables,
            weekOf,   // Monday of the week to display; default to this week
            $weekOf,  // Control to specify weekOf
            publicApi;


        function init( selector ) {
            cacheDom( selector );
            bindEvents();

            weekOf = new Date();
            weekOf = normalizeDateToMonday( weekOf );
            $weekOf.datepicker();
            $weekOf.datepicker( 'option', 'showAnim', 'slideDown' );
            $weekOf.datepicker( 'setDate', weekOf );
            $( '#pdf-attendance' ).attr( 'href', 'pdf.php?attendance&week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
        }


        function cacheDom( selector ) {
            $page        = $( selector );
            $weekOf      = $page.find( 'input[name=week-of]' );
            $schedTables = $page.find( 'div.attendance-page-schedules' );
        }


        function bindEvents() {
            $weekOf.on( 'change', onChangeWeekOf );
            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );
            Students.subscribe( 'load-records', whenStudentsLoaded );
            Schedules.subscribe( 'load-records', whenSchedulesLoaded );

            Students.subscribe( 'insert-record', generateAttendanceSheets );
            Students.subscribe( 'update-record', generateAttendanceSheets );
            Students.subscribe( 'remove-record', generateAttendanceSheets );

            Schedules.subscribe( 'insert-record', generateAttendanceSheets );
            Schedules.subscribe( 'update-record', generateAttendanceSheets );
            Schedules.subscribe( 'remove-record', generateAttendanceSheets );
        }


        function generateAttendanceSheets() {
            if ( !generateAttendanceSheets.hasClassrooms ) return;
            if ( !generateAttendanceSheets.hasStudents ) return;
            if ( !generateAttendanceSheets.hasSchedules ) return;
            $schedTables.empty();
            for ( var classroomId in Classrooms.records ) {
                $schedTables.append( generateClassroomTable( classroomId ) );
            }
        }

        generateAttendanceSheets.hasClassrooms = false;
        generateAttendanceSheets.hasStudents   = false;
        generateAttendanceSheets.hasSchedules  = false;


        function generateClassroomTable( classroomId ) {

            var MoAbbrvs = [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
            var classroom,
                $table,
                $thead,
                $tbody,
                $tr,
                $th;

            classroom = Classrooms.records[ classroomId ];
            $table    = $( '<table>' );
            $thead    = $( '<thead>' );
            $tr       = $( '<tr>' );
            $th       = $( '<th colspan="7">' ).text( 'Attendance' );
            $tr.append( $th );
            $thead.append( $tr );

            $tr = $( '<tr>' );
            $th = $( '<th colspan="7">' )
                    .append( $( '<span class="classroom pull-left">' + classroom.label + '</span>' ) )
                    .append( $( '<span class="week-of pull-right">Week Of ' + MoAbbrvs[ $weekOf.datepicker( 'getDate' ).getMonth() ] + ' ' + $weekOf.datepicker( 'getDate' ).getDate() + ', ' + (1900 + $weekOf.datepicker( 'getDate' ).getYear()) + '</span>' ) );
            $tr.append( $th );
            $tr.append( $th );
            $thead.append( $tr );

            var weekOf = $weekOf.datepicker( 'getDate' );
            $tr        = $( '<tr>' );
            $tr.append( $( '<th>' ).addClass( 'attendance-schedule-name' ).text( 'Student' ) );
            $tr.append( $( '<th>' ).addClass( 'foopy' ).html( 'Mon<br>' + weekOf.getDate() + '-' + MoAbbrvs[ weekOf.getMonth() ] ) );
            weekOf.setDate( weekOf.getDate() + 1 );
            $tr.append( $( '<th>' ).addClass( 'foopy' ).html( 'Tue<br>' + weekOf.getDate() + '-' + MoAbbrvs[ weekOf.getMonth() ] ) );
            weekOf.setDate( weekOf.getDate() + 1 );
            $tr.append( $( '<th>' ).addClass( 'foopy' ).html( 'Wed<br>' + weekOf.getDate() + '-' + MoAbbrvs[ weekOf.getMonth() ] ) );
            weekOf.setDate( weekOf.getDate() + 1 );
            $tr.append( $( '<th>' ).addClass( 'foopy' ).html( 'Thu<br>' + weekOf.getDate() + '-' + MoAbbrvs[ weekOf.getMonth() ] ) );
            weekOf.setDate( weekOf.getDate() + 1 );
            $tr.append( $( '<th>' ).addClass( 'foopy' ).html( 'Fri<br>' + weekOf.getDate() + '-' + MoAbbrvs[ weekOf.getMonth() ] ) );
            $tr.append( $( '<th>' ).addClass( 'attendance-schedule-notes' ).text( 'Notes' ) );

            $thead.append( $tr );
            $table.attr( 'data-classroom-id', classroomId );

            $tbody = $( '<tbody>' );
            if ( Students.classrooms[ classroomId ] ) {
                for ( var i = 0; i < Students.classrooms[ classroomId ].length; i++ ) {
                    $tbody.append( generateStudentRow( Students.classrooms[ classroomId ][ i ] ) );
                }
            }
            $table.append( $thead );
            $table.append( $tbody );
            return $table;
        }

        function generateStudentRow( studentId ) {

            function compareDates( a, b ) {
                if ( a.getFullYear() < b.getFullYear() ) return -1;
                if ( a.getFullYear() > b.getFullYear() ) return 1;
                if ( a.getMonth() < b.getMonth() ) return -1;
                if ( a.getMonth() > b.getMonth() ) return 1;
                if ( a.getDate() < b.getDate() ) return -1;
                if ( a.getDate() > b.getDate() ) return 1;
                return 0;
            }

            var decoder = [
                [ 0x0001, 0x0020, 0x0400 ],
                [ 0x0002, 0x0040, 0x0800 ],
                [ 0x0004, 0x0080, 0x1000 ],
                [ 0x0008, 0x0100, 0x2000 ],
                [ 0x0010, 0x0200, 0x4000 ]
            ];

            var student = Students.records[ studentId ];
            if ( 1 != student.enrolled ) {
                return;
            }
            var schedules = Schedules.students[ studentId ];
            if ( undefined === schedules ) {
                return;
            }
            var $tr = $( '<tr class="student-row">' );
            var $td = $( '<td class="attendance-schedule-name">' ).text( Students.records[ studentId ].first_name + ' ' + Students.records[ studentId ].family_name );
            $tr.append( $td );
            $tr.data( 'student-id', studentId );

            var thisWeek = new Date( $weekOf.val() );
            var today    = new Date( schedules[ 0 ].start_date );
            var notes    = {
                'FD' : 0,
                'HD' : 0,
                'HDL': 0
            };

            var j = 0;
            for ( var i = 0; i < 5; i++ ) {
                while ( (j + 1) < schedules.length ) {
                    var next = new Date( schedules[ j + 1 ].start_date );
                    if ( compareDates( next, thisWeek ) > 1 ) break;
                    today = next;
                    j++;
                }

                var s = { 'am': false, 'pm': false, 'lunch': false };
                if ( schedules[ j ].schedule & decoder[ i ][ 0 ] ) s.am = true;
                if ( schedules[ j ].schedule & decoder[ i ][ 1 ] ) s.lunch = true;
                if ( schedules[ j ].schedule & decoder[ i ][ 2 ] ) s.pm = true;
                if ( s.am && s.pm ) {
                    $tr.append( $( '<td>' ) );
                    notes.FD++;
                } else if ( s.am ) {
                    $tr.append( $( '<td>' ) );
                    if ( s.lunch ) {
                        notes.HDL++;
                    } else {
                        notes.HD++;
                    }
                } else if ( s.pm ) {
                    $tr.append( $( '<td>' ) );
                    if ( s.lunch ) {
                        notes.HDL++;
                    } else {
                        notes.HD++;
                    }
                } else {
                    $tr.append( $( '<td class="absent">' ) );
                }
                thisWeek.setDate( thisWeek.getDate() + 1 );
                today.setDate( today.getDate() + 1 );
            }
            var text = '';
            if ( notes.FD ) {
                text = notes.FD + 'FD';
            } else if ( notes.HD ) {
                text = notes.HD + 'HD';
            } else if ( notes.HDL ) {
                text = notes.HDL + 'HDL';
            }

            $tr.append( $( '<td class="attendance-schedule-notes">' ).text( text ) );


            return $tr;
        }

        function onChangeWeekOf() {
            weekOf = $weekOf.datepicker( 'getDate' );
            weekOf = normalizeDateToMonday( weekOf );
            $weekOf.datepicker( 'setDate', weekOf ).blur();
            generateAttendanceSheets();
            $( '#pdf-attendance' ).attr( 'href', 'pdf.php?attendance&week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
        }

        function whenClassroomsLoaded() {
            generateAttendanceSheets.hasClassrooms = true;
            generateAttendanceSheets();
        }


        function whenStudentsLoaded() {
            generateAttendanceSheets.hasStudents = true;
            generateAttendanceSheets();
        }

        function whenSchedulesLoaded() {
            generateAttendanceSheets.hasSchedules = true;
            generateAttendanceSheets();
        }


        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Table showing sign-in schedules
     ********************************************************************************/
    var SigninPage = (function () {
        var $page,
            $contents,
            weekOf,   // Monday of the week to display; default to this week
            $weekOf,  // Control to select weekOf
            publicApi;

        function init( selector ) {
            cacheDom( selector );
            bindEvents();

            weekOf = new Date();
            weekOf = normalizeDateToMonday( weekOf );
            $weekOf.datepicker();
            $weekOf.datepicker( 'option', 'showAnim', 'slideDown' );
            $weekOf.datepicker( 'setDate', weekOf );
            $( '#pdf-signin' ).attr( 'href', 'pdf.php?signin&week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
        }

        function cacheDom( selector ) {
            $page     = $( selector );
            $weekOf   = $page.find( 'input[name=week-of]' );
            $contents = $page.find( '.signin-page-contents' );
        }

        function bindEvents() {
            $weekOf.on( 'change', onChangeWeekOf );
            Classrooms.subscribe( 'load-records', whenClassroomsLoaded );
            Students.subscribe( 'load-records', whenStudentsLoaded );
            Schedules.subscribe( 'load-records', whenSchedulesLoaded );

            Students.subscribe( 'insert-record', generateSigninSheets );
            Students.subscribe( 'update-record', generateSigninSheets );
            Students.subscribe( 'remove-record', generateSigninSheets );

            Schedules.subscribe( 'insert-record', generateSigninSheets );
            Schedules.subscribe( 'update-record', generateSigninSheets );
            Schedules.subscribe( 'remove-record', generateSigninSheets );
        }

        function generateSigninSheets() {
            if ( !generateSigninSheets.hasClassrooms ) return;
            if ( !generateSigninSheets.hasStudents ) return;
            if ( !generateSigninSheets.hasSchedules ) return;
            $contents.empty();
            for ( var classroomId in Classrooms.records ) {
                $contents.append( generateClassroomTable( classroomId ) );
            }
        }

        generateSigninSheets.hasClassrooms = false;
        generateSigninSheets.hasStudents   = false;
        generateSigninSheets.hasSchedules  = false;

        function generateClassroomTable( classroomId ) {
            var MoAbbrvs = [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];
            var classroom,
                $table,
                $thead,
                $tbody,
                $tr,
                $th;

            classroom = Classrooms.records[ classroomId ];
            $table    = $( '<table>' );
            $thead    = $( '<thead>' );
            $tr       = $( '<tr>' );

            $th = $( '<th colspan="11">' ).text( 'Signin' );
            $tr.append( $th );
            $thead.append( $tr );

            $tr = $( '<tr>' );
            $th = $( '<th colspan="11">' )
                    .append( $( '<span class="classroom pull-left">' + classroom.label + '</span>' ) )
                    .append( $( '<span class="week-of pull-right">Week Of ' + MoAbbrvs[ $weekOf.datepicker( 'getDate' ).getMonth() ] + ' ' + $weekOf.datepicker( 'getDate' ).getDate() + ', ' + (1900 + $weekOf.datepicker( 'getDate' ).getYear()) + '</span>' ) );
            $tr.append( $th );
            $thead.append( $tr );

            $tr = $( '<tr>' );
            $tr.append( $( '<th rowspan="3">' ).text( 'Student' ) );
            $tr.append( $( '<th colspan="2">' ).text( 'Mon' ) );
            $tr.append( $( '<th colspan="2">' ).text( 'Tue' ) );
            $tr.append( $( '<th colspan="2">' ).text( 'Wed' ) );
            $tr.append( $( '<th colspan="2">' ).text( 'Thu' ) );
            $tr.append( $( '<th colspan="2">' ).text( 'Fri' ) );
            $thead.append( $tr );

            $tr = $( '<tr>' );
            $tr.append( $( '<th>' ).text( "In" ) );
            $tr.append( $( '<th rowspan="2">' ).text( "Initial" ) );
            $tr.append( $( '<th>' ).text( "In" ) );
            $tr.append( $( '<th rowspan="2">' ).text( "Initial" ) );
            $tr.append( $( '<th>' ).text( "In" ) );
            $tr.append( $( '<th rowspan="2">' ).text( "Initial" ) );
            $tr.append( $( '<th>' ).text( "In" ) );
            $tr.append( $( '<th rowspan="2">' ).text( "Initial" ) );
            $tr.append( $( '<th>' ).text( "In" ) );
            $tr.append( $( '<th rowspan="2">' ).text( "Initial" ) );
            $thead.append( $tr );

            $tr = $( '<tr>' );
            $tr.append( $( '<th>' ).text( "Out" ) );
            $tr.append( $( '<th>' ).text( "Out" ) );
            $tr.append( $( '<th>' ).text( "Out" ) );
            $tr.append( $( '<th>' ).text( "Out" ) );
            $tr.append( $( '<th>' ).text( "Out" ) );
            $thead.append( $tr );

            $tr = $( '<tr>' );
            $tr.append( $( '<th>' ).addClass( 'signin-schedule-name' ).text( 'Student' ) );

            $table.attr( 'data-classroom-id', classroomId );
            $tbody = $( '<tbody>' );
            if ( Students.classrooms[ classroomId ] ) {
                for ( var i = 0; i < Students.classrooms[ classroomId ].length; i++ ) {
                    $tbody.append( generateStudentRow( Students.classrooms[ classroomId ][ i ] ) );
                }
            }
            $table.append( $thead );
            $table.append( $tbody );
            return $table;
        }

        function generateStudentRow( studentId ) {
            function compareDates( a, b ) {
                if ( a.getFullYear() < b.getFullYear() ) return -1;
                if ( a.getFullYear() > b.getFullYear() ) return 1;
                if ( a.getMonth() < b.getMonth() ) return -1;
                if ( a.getMonth() > b.getMonth() ) return 1;
                if ( a.getDate() < b.getDate() ) return -1;
                if ( a.getDate() > b.getDate() ) return 1;
                return 0;
            }

            var decoder = [
                [ 0x0001, 0x0020, 0x0400 ],
                [ 0x0002, 0x0040, 0x0800 ],
                [ 0x0004, 0x0080, 0x1000 ],
                [ 0x0008, 0x0100, 0x2000 ],
                [ 0x0010, 0x0200, 0x4000 ]
            ];
            var student = Students.records[ studentId ];
            if ( 1 != student.enrolled ) {
                return;
            }


            var schedules = Schedules.students[ studentId ];
            if ( undefined === schedules ) {
                return;
            }
            var $tr = $( '<tr class="student-row">' );
            var $td = $( '<td class="signin-schedule-name">' ).text( Students.records[ studentId ].first_name + ' ' + Students.records[ studentId ].family_name );
            $tr.append( $td );

            var thisWeek = new Date( $weekOf.val() );
            var today    = new Date( schedules[ 0 ].start_date );

            var j = 0;
            for ( var i = 0; i < 10; i++ ) {
                while ( (j + 1) < schedules.length ) {
                    var next = new Date( schedules[ j + 1 ].start_date );
                    if ( compareDates( next, thisWeek ) > 1 ) break;
                    today = next;
                    j++;
                }
                /*
                 if ((schedules[j].schedule & decoder[i][0])
                 || (schedules[j].schedule & decoder[i][1])
                 || (schedules[j].schedule & decoder[i][2])) {
                 }

                 */
                var $div = $( '<div class="in-out">' ).html( '&nbsp;' );
                var $td  = $( '<td>' );
                $td.append( $div ).append( '&nbsp;' );

                $tr.append( $td );
            }


            $tr.data( 'student-id', studentId );
            return $tr;
        }


        function onChangeWeekOf() {
            weekOf = $weekOf.datepicker( 'getDate' );
            weekOf = normalizeDateToMonday( weekOf );
            $weekOf.datepicker( 'setDate', weekOf ).blur();
            $( '#pdf-signin' ).attr( 'href', 'pdf.php?signin&week=' + (weekOf.getFullYear()) + '-' + (weekOf.getMonth() + 1) + '-' + weekOf.getDate() );
        }

        function whenClassroomsLoaded() {
            generateSigninSheets.hasClassrooms = true;
            generateSigninSheets();
        }


        function whenStudentsLoaded() {
            generateSigninSheets.hasStudents = true;
            generateSigninSheets();
        }


        function whenSchedulesLoaded() {
            generateSigninSheets.hasSchedules = true;
            generateSigninSheets();
        }


        publicApi = {
            init: init
        };
        return publicApi;
    })();


    /********************************************************************************
     * Document on-ready handler
     ********************************************************************************/
    $( function () {
        var $tabs   = $( '.tab' );  // All of the tabs in the top-level menu, <a href="#target">
        var oldhash = '';           // Previous hash fragment in URL


        // DOM elements identified by the href attributes in the $tabs
        var targets = $tabs.map( function () {
            return this.hash;   // Return the anchor part of the URL
        } ).get();

        //
        var $panels = $( targets.join( ',' ) );

        function showPage( id ) {
            // If no value was given, let's take the first panel
            if ( !id ) id = targets[ 0 ];
            else id = id.split( '/' )[ 0 ];
            $tabs.removeClass( 'active' ).filter( function () {
                return (this.hash === id);
            } ).addClass( 'active' );
            $panels.hide();
            var $panel = $panels.filter( id );
            $panel.show();
        }

        // Prevent user from leaving a panel with unsaved changes
        $( window ).on( 'hashchange', function () {
            if ( ( $panels.filter( ':visible' ).find( '.modified' ).length ) && ( location.hash !== oldhash ) ) {
                alert( "You have unsaved changes on this page" );
                location.hash = oldhash;
                return false;
            } else {
                oldhash = location.hash;
                showPage( location.hash );
            }
        } );

        // Warn user  if they try to leave the page with unsaved changes
        $( window ).on( 'beforeunload', function () {
            if ( $panels.filter( ':visible' ).find( '.modified' ).length ) {
                return 'Are you sure you want to leave?';
            }
        } );


        Classrooms.init();
        Students.init();
        Schedules.init();

        ClassroomsPage.init( '#classrooms-page' );
        ClassroomsTable.init( '#classrooms-table' );
        ClassroomPropsDlg.init( '#classroom-dlg' );

        EnrollmentPage.init( '#enrollment-page' );
        EnrollmentTable.init( '#students-table' );
        StudentPropsDlg.init( '#student-dlg' );
        ScheduleDlg.init( '#schedule-dlg' );
        SigninPage.init( '#signin-page' );
        AttendancePage.init( '#attendance-page' );

        ClassroomController.load();
        StudentController.load();
        SchedulesController.load();
        if ( targets.indexOf( location.hash ) !== -1 ) {
            oldhash = location.hash;
            showPage( location.hash );
        } else {
            oldhash = '';
            showPage( '' );
        }
    } );


})( this, jQuery );
