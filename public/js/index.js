;(function ( global, $ ) {
    'use strict';

    var Classrooms = (function () {
        var classrooms = [];

        function load() {
            Attend.loadAnother();
            $.ajax( {
                'url'   : 'api/classrooms',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );

                    // Must be expressed as "Classrooms.classrooms", rather than simply referring to "classrooms"
                    // (a la closure). Not exactly sure why, but "classrooms" alone doesn't work - it refers to
                    // a separate object somehow.
                    json.sort( function ( a, b ) {
                        if ( a.ordering > b.ordering ) return 1;
                        if ( a.ordering < b.ordering ) return -1;
                        return 0;
                    } );
                    Classrooms.classrooms = json;
                    AttendanceTab.build();
                    SigninTab.build();
                    Attend.doneLoading();
                },

                'error': function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
                }
            } )
        }

        return {
            'classrooms': classrooms,
            'load'      : load
        };
    })();


    var Students = (function () {
        var students = [];

        function load() {
            Attend.loadAnother();
            $.ajax( {
                'url'   : 'api/students',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );
                    for ( var i = 0; i < json.length; i++ ) {
                        var student = json[ i ];
                        if ( 1 !== student.Enrolled ) continue;

                        if ( !(student.ClassroomId in Students.students) ) {
                            Students.students[ student.ClassroomId ] = [];
                        }
                        Students.students[ student.ClassroomId ].push( student );
                    }

                    for ( i = 0; i < Students.students.length; i++ ) {
                        if ( !Students.students[ i ] ) continue;
                        Students.students[ i ].sort( function ( a, b ) {
                            if ( a.FamilyName > b.FamilyName ) return 1;
                            if ( a.FamilyName < b.FamilyName ) return -1;
                            if ( a.FirstName > b.FirstName ) return 1;
                            if ( a.FirstName < b.FirstName ) return -1;
                            return 0;
                        } );
                    }
                    AttendanceTab.build();
                    SigninTab.build();
                    Attend.doneLoading();
                },
                'error'  : function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
                }
            } );
        }

        return {
            'students': students,
            'load'    : load
        }
    })();


    var Schedules = (function () {
        var schedules = [];   // Schedules by student id

        function load() {
            Attend.loadAnother();
            $.ajax( {
                'url'   : 'api/schedules',
                'method': 'get',

                'success': function ( json ) {
                    console.log( json );
                    for ( var i = 0; i < json.length; i++ ) {
                        var sched = json[ i ];
                        if ( !( sched.StudentId in Schedules.schedules) ) {
                            Schedules.schedules[ sched.StudentId ] = [];
                        }
                        Schedules.schedules[ sched.StudentId ].push( sched );
                    }
                    AttendanceTab.build();
                    SigninTab.build();
                    Attend.doneLoading();
                },
                'error'  : function ( xhr ) {
                    console.log( xhr );
                    Attend.doneLoading();
                }
            } )
        }

        return {
            'schedules': schedules,
            'load'     : load
        };
    })();


    var AttendanceTab = (function () {
        var $tab,
            $weekOf,
            $attendance;

        function init( selector ) {
            $tab    = $( selector );
            $weekOf = $tab.find( '[name=week-of]' );
            $weekOf.datepicker();
            $attendance = $tab.find( '.attendance-page-schedules' );

            $weekOf.datepicker( 'setDate', Attend.getMonday( new Date() ) );
            $( '#pdf-attendance' ).attr( 'href', 'pdf.php?attendance&week=' + $weekOf.val() );
            $weekOf.on( 'change', function () {
                $( this ).datepicker( 'setDate', Attend.getMonday( new Date( $( this ).val() ) ) );
                $( this ).blur();
                $( '#pdf-attendance' ).attr( 'href', 'pdf.php?attendance&week=' + $( this ).val() );
                build();
            } );
        }

        function build() {
            if ( !Classrooms.classrooms.length ) {
                return;
            }
            if ( !Students.students.length ) {
                return;
            }
            if ( !Schedules.schedules.length ) {
                return;
            }
            buildAttendanceTables( Classrooms.classrooms, Students.students, Schedules.schedules );
        }

        function buildAttendanceTables( classrooms, students, schedules ) {
            console.log( 'Building attendance tables' );
            $attendance.empty();
            Attend.loadAnother();

            for ( var i = 0; i < classrooms.length; i++ ) {
                $attendance.append( $( '<h3>' ).text( classrooms[ i ].label ) );
                var $table = buildAttendanceTable( classrooms[ i ], students, schedules );

                $table.DataTable( {
                    'searching': false,
                    'paging'   : false,
                    'ordering' : false,
                    'info'     : false
                } );

                $attendance.append( $table );
            }
            Attend.doneLoading();
        }

        function buildAttendanceTable( classroom, students, schedules ) {
            var days   = [ 'Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri' ];
            var months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];

            var $table = $( '<table class="table table-striped table-bordered">' );
            var $thead = $( '<thead>' );
            $table.append( $thead );

            var $tbody = $( '<tbody>' );
            $table.append( $tbody );

            var $tr = $( '<tr>' );
            $tr.append( $( '<th>Name</th>' ) );

            var weekOf = $weekOf.val();
            var cur    = new Date( weekOf );
            for ( var i = 0; i < 5; i++ ) {
                cur = cur.addDays( 1 );
                $tr.append( $( '<th>' + days[ cur.getDay() ] + '<br/>' + months[ cur.getMonth() ] + ' ' + cur.getDate() + '</th>' ) );
            }

            $tr.append( $( '<th>Summary</th>' ) );
            $thead.append( $tr );

            if ( students[ classroom.Id ] ) {
                for ( var i = 0; i < students[ classroom.Id ].length; i++ ) {
                    var $tr = buildStudentRow( students[ classroom.Id ][ i ], schedules );
                    $tbody.append( $tr );
                }
            }
            return $table;
        }


        function buildStudentRow( student, schedules ) {
            var decoder = [
                [ 0x0001, 0x0020, 0x0400 ],
                [ 0x0002, 0x0040, 0x0800 ],
                [ 0x0004, 0x0080, 0x1000 ],
                [ 0x0008, 0x0100, 0x2000 ],
                [ 0x0010, 0x0200, 0x4000 ]
            ];

            var sched = schedules[ student.Id ][ schedules[ student.Id ].length - 1 ].Schedule;
            var notes = {
                'FD' : 0,
                'HDL': 0,
                'HD' : 0
            };
            var $tr   = $( '<tr>' );
            $tr.append( $( '<td>' ).text( student.FamilyName + ', ' + student.FirstName ) );
            for ( var i = 0; i < 5; i++ ) {
                var $cell = buildDayCell( sched, decoder[ i ] );
                $tr.append( $cell.td );
                if ( $cell.p ) {
                    notes[ $cell.p ]++;
                }
            }
            var summary = [];
            if ( notes[ 'FD' ] ) {
                summary.push( notes[ 'FD' ] + 'FD' );
            }
            if ( notes[ 'HD' ] ) {
                summary.push( notes[ 'HD' ] + 'HD' );
            }
            if ( notes[ 'HDL' ] ) {
                summary.push( notes[ 'HDL' ] + 'HDL' );
            }
            $tr.append( $( '<td>' ).text( summary.join() ) );
            return $tr;
        }


        function buildDayCell( sched, decoder ) {
            var $td = $( '<td>' );
            var p;
            if ( ( sched & decoder[ 0 ]) && (sched & decoder[ 2 ]) ) {
                $td.text( 'FD' );
                p = 'FD';
            } else if ( sched & decoder[ 0 ] ) {
                if ( sched & decoder[ 1 ] ) {
                    $td.text( 'HDL' );
                    p = 'HDL';
                } else {
                    $td.text( 'HD' );
                    p = 'HD';
                }

            } else if ( sched & decoder[ 2 ] ) {
                if ( sched & decoder[ 1 ] ) {
                    $td.text( 'HDL' );
                    p = 'HDL';
                } else {
                    $td.text( 'HD' );
                    p = 'HD';
                }
            } else {
                $td.addClass( 'dark' );
                p = '';
            }
            return {
                'td': $td,
                'p' : p
            };
        }

        return {
            'init' : init,
            'build': build
        };
    })();


    var SigninTab = (function () {
        var $tab,
            $weekOf,
            $signin
                ;

        function init( selector ) {
            $tab    = $( selector );
            $weekOf = $tab.find( '[name=week-of]' );
            $weekOf.datepicker();
            $signin = $tab.find( '.attendance-page-signin' );

            $weekOf.datepicker( 'setDate', Attend.getMonday( new Date() ) );
            $( '#pdf-signin' ).attr( 'href', 'pdf.php?signin&week=' + $weekOf.val() );

            $weekOf.on( 'change', function onChange_weekOf() {
                $( this ).datepicker( 'setDate', Attend.getMonday( new Date( $( this ).val() ) ) );
                $( this ).blur();
                $( '#pdf-signin' ).attr( 'href', 'pdf.php?signin&week=' + $( this ).val() );
                build();
            } );
        }

        function build() {
            if ( !Classrooms.classrooms.length ) {
                return;
            }
            if ( !Students.students.length ) {
                return;
            }
            if ( !Schedules.schedules.length ) {
                return;
            }
            buildSigninTables( Classrooms.classrooms, Students.students, Schedules.schedules );
        }

        function buildSigninTables( classrooms, students, schedules ) {
            console.log( "Building sign-in tables" );
            $signin.empty();
            Attend.loadAnother();

            for ( var i = 0; i < classrooms.length; i++ ) {
                $signin.append( $( '<h3>' ).text( classrooms[ i ].label ) );
                var $table = buildSigninTable( classrooms[ i ], students, schedules );

                $table.DataTable( {
                    'searching': false,
                    'paging'   : false,
                    'ordering' : false,
                    'info'     : false
                } );

                $signin.append( $table );
            }
            Attend.doneLoading();
        }

        function buildSigninTable( classroom, students, schedules ) {
            var days   = [ 'Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri' ];
            var months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
            var $table = $( '<table class="table table-striped table-bordered">' );
            var $thead = $( '<thead>' );
            $table.append( $thead );

            var $tbody = $( '<tbody>' );
            $table.append( $tbody );

            var $tr = $( '<tr>' );
            $tr.append( $( '<th>Name</th>' ) );

            var weekOf = $weekOf.val();
            var cur    = new Date( weekOf );
            for ( var i = 0; i < 5; i++ ) {
                cur = cur.addDays( 1 );
                $tr.append( $( '<th>' + days[ cur.getDay() ] + '<br/>' + months[ cur.getMonth() ] + ' ' + cur.getDate() + '</th>' ) );
            }
            $thead.append( $tr );

            if ( students[ classroom.Id ] ) {
                for ( var i = 0; i < students[ classroom.Id ].length; i++ ) {
                    var $tr = buildStudentRow( students[ classroom.Id ][ i ], schedules );
                    $tbody.append( $tr );
                }
            }
            return $table;
        }

        function buildStudentRow( student, schedules ) {
            var decoder = [
                [ 0x0001, 0x0020, 0x0400 ],
                [ 0x0002, 0x0040, 0x0800 ],
                [ 0x0004, 0x0080, 0x1000 ],
                [ 0x0008, 0x0100, 0x2000 ],
                [ 0x0010, 0x0200, 0x4000 ]
            ];

            var sched = schedules[ student.Id ][ schedules[ student.Id ].length - 1 ].Schedule;
            var $tr   = $( '<tr>' );
            $tr.append( $( '<td>' ).text( student.FamilyName + ', ' + student.FirstName ) );
            for ( var i = 0; i < 5; i++ ) {
                var $td = buildDayCell( sched, decoder[ i ] );
                $tr.append( $td );
            }

            return $tr;
        }

        function buildDayCell( sched, decoder ) {
            var $td = $( '<td>' );

            return $td;
        }

        return {
            'init' : init,
            'build': build
        };
    })();


    $( function () {
        console.log( "Index page ready" );
        AttendanceTab.init( '#attendance-tab' );
        SigninTab.init( '#signin-tab' );
        Classrooms.load();
        Students.load();
        Schedules.load();
        $( '#tabs' ).tabs().show();
    } );


})( this, jQuery );
