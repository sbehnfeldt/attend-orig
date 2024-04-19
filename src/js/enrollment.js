import $ from 'jquery';
import 'jquery-ui/ui/widgets/tabs';
import 'jquery-ui/ui/widgets/dialog';
import 'jquery-ui/ui/widgets/datepicker'
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons';
import 'datatables.net-select';
import Attend from "./attend";
import AttendApi from './attend-api';

'use strict';



let EnrollmentTab = (function (selector) {
    let classrooms = [];
    let students = [];

    let table = new DataTable(selector, {
        data: students,
        order: [[1, 'asc']],
        select: true,
        columns: [
            {data: "Id"},
            {data: "FamilyName"},
            {data: "FirstName"}, {
                data: "Enrolled",
                render: function (data) {
                    return '<input type=checkbox ' + (1 == data ? 'checked ' : '') + ' disabled />';
                }
            }, {
                data: "ClassroomId",
                render: function (data) {
                    let classroom = classrooms.find((e) => e.Id === data );
                    return classroom.Label;
                }
            }
        ],
    });

    function load() {
        Attend.loadAnother();
        try {
            let p = Promise.all([ AttendApi.classrooms.select(), AttendApi.students.select()])
                .then((values) => {
                    classrooms = values[0];
                    students = values[1];
                })
                .catch((err) => {
                    console.error(err);
                    alert( "Students didn't load");
                    Attend.doneLoading();
                });
            return p;
        } catch(e) {
            console.log(e);
        } finally {
            Attend.doneLoading();
        }
    }

    function populate() {
        table.clear();
        for (let i = 0; i < students.length; i++) {
            table.row.add(students[i]);
        }
        table.draw();
        return this;
    }

    return {load, populate};
})('#enrollment-table');


let StudentPropsDlg = (function (selector) {
    let $self,
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

    $self       = $(selector);
    $form       = $self.find('form');
    $studentId  = $form.find('[name=Id]');
    $familyName = $self.find('[name=FamilyName]');
    $firstName  = $self.find('[name=FirstName]');
    $classrooms = $self.find('[name=ClassroomsList]');
    $enrolled   = $self.find('[name=Enrolled]');
    $startDate  = $self.find('[name=WeekOf]');
    $list       = $form.find('[name=SchedulesList]');

    $boxes    = $form.find('table.schedule-table input[type=checkbox]');
    $buttons  = $form.find('table.schedule-table button');
    $required = $form.find('.required');

    dialog = $self.dialog({
        "autoOpen": false,
        "modal": true,
        "width": "450px",
        "buttons": {
            "Submit": function onSubmit() {
                if (validate()) {
                    submit();
                    close();
                }
            },
            "Cancel": function onCancel() {
                StudentPropsDlg.close();
            }
        }
    });
    $startDate.datepicker({
        'dateFormat': 'yy-mm-dd'
    });

    // When a schedule button is clicked, Set/clear all the checkboxes in that button's row or column
    $('.sched-button').on('click', function () {
        let key,   // Button's bit-map of checkbox values
            $b;    // Subset of checkboxes to set/clear

        key = $(this).data('key');

        // From the set of all checkboxes in the schedule table, select only those whose values are found
        // in the schedule button's bitmap key
        $b = $boxes.filter(function () {

            // Return true of the checkbox's value is turned on in the button's bitmap key
            return ($(this).val() & key);
        });

        // If all of the checkboxes are already checked, clear them all; otherwise, set them all
        $b.prop('checked', $b.length !== $b.filter(':checked').length);
    });

    $buttons.on('click', function () {
        return false;
    });

    $boxes.on('change', function () {
        // No op
    });

    $list.on('change', function () {
        let id    = $studentId.val();
        let idx   = $(this)[0].selectedIndex;
        // let sched = Schedules.records[id][idx].Schedule;
        $boxes.each(function (idx, elem) {
            if ($(elem).val() & sched) {
                $(elem).prop('checked', true);
            } else {
                $(elem).prop('checked', false);
            }
        });
    });

    // $classrooms is required only if student is enrolled
    $enrolled.on('click', function () {
        if ($(this).prop('checked')) {
            $classrooms.addClass('required');
        } else {
            $classrooms.removeClass('required');
        }
    });


    function open(student) {
        clear();
        if (student) {
            populate(student);
        } else {
            $startDate.datepicker('setDate', Attend.getMonday(new Date()));
        }
        dialog.dialog('open');
    }

    function close() {
        dialog.dialog('close');
    }

    function clear() {
        $form[0].reset();
        $required.removeClass('missing');
        $classrooms.addClass('required');
        $list.empty();
        $list.addClass('hidden');
    }

    function populate(student) {
        let $opt;

        $studentId.val(student.Id);
        $familyName.val(student.FamilyName);
        $firstName.val(student.FirstName);
        $classrooms.val(student.ClassroomId);
        $enrolled.prop('checked', (1 == student.Enrolled));
        $startDate.datepicker('setDate', Attend.getMonday(new Date()));

        $list.removeClass('hidden');
        // for (let i = 0; i < Schedules.records[student.Id].length; i++) {
        //     let s = Schedules.records[student.Id][i];
        //     $opt  = $('<option>').text(s.StartDate.split('T')[0]).val(s.Id);
        //     $list.append($opt);
        // }
        $list.trigger('change');
    }

    function validate() {
        let valid = true;
        $required.each(function (i, e) {
            if (!$(e).val()) {
                $(e).addClass('missing');
                valid = false;
            } else {
                $(e).removeClass('missing');
            }
        });
        return valid;
    }

    function submit() {
        let id,
            student,
            map;

        id      = $studentId.val();
        student = {
            "FamilyName": $familyName.val(),
            "FirstName": $firstName.val(),
            "Enrolled": (true === $enrolled.prop('checked')) ? 1 : 0,
            "ClassroomId": $classrooms.val() ? $classrooms.val() : null
        };

        map = 0;
        $boxes.each(function (i, e) {
            if ($(e).prop('checked')) {
                map += parseInt($(e).val(), 16);
            }
        });

        if (!id) {
            // Insert new student and schedule
            insert(student, {
                StartDate: $startDate.val(),
                Schedule: map
            });

        } else {
            let idx = $list.prop('selectedIndex');
            // let cur = Schedules.records[id][idx];

            // console.log(cur);
            // if (cur.schedule == map) {
                // Update student, leave schedule unchanged
                // update(id, student, null);
            // } else {
                // Update student, add new schedule
                // update(id, student, {
                //     StartDate: $startDate.val(),
                //     Schedule: map
                // });
            // }
        }
        StudentPropsDlg.close();
    }


    // Insert new student, new schedule
    function insert(student, schedule) {
        Attend.loadAnother();
        $.ajax({
            "url": "api/students",
            "method": "post",
            "data": student,

            "dataType": "json",
            "success": function (json) {
                console.log(json);
                Attend.loadAnother();
                $.ajax({
                    'url': 'api/students/' + json.Id,
                    'method': 'get',
                    'success': function (json) {
                        console.log(json);
                        EnrollmentTab.insert(json);
                        Attend.loadAnother();   // Get student record just loaded
                        schedule.StudentId = json.Id;
                        $.ajax({
                            "url": "api/schedules",
                            "method": "post",
                            "data": schedule,

                            "dataType": "json",
                            "success": function (json) {
                                $.ajax({
                                    'url': 'api/schedules/' + json.Id,
                                    'method': 'get',
                                    'success': function (json) {
                                        console.log(json);
                                        // Schedules.insert(json);
                                        Attend.doneLoading();
                                    },
                                    'error': function (xhr) {
                                        console.log(xhr);
                                        Attend.doneLoading();
                                    }
                                })
                            },
                            "error": function (xhr) {
                                console.log(xhr);
                                Attend.doneLoading();
                                alert("Error");
                            }
                        });
                        Attend.doneLoading();
                    },

                    'error': function (xhr) {
                        console.log(xhr);
                        Attend.doneLoading();
                    }
                });

                Attend.doneLoading();
            },
            "error": function (xhr) {
                console.log(xhr);
                Attend.doneLoading();
                alert("Error");
            }
        });
    }

    function update(id, student, schedule) {
        Attend.loadAnother();
        $.ajax({
            "url": "api/students/" + id,
            "method": "put",
            "data": student,

            "success": function (json) {
                student.Id = id;

                EnrollmentTab.redrawRow(student);
                if (schedule) {
                    schedule.StudentId = id;

                    let d1 = $startDate.val();
                    // for (let i = 0; i < Schedules.records[id].length; i++) {
                    //     if (d1 === Schedules.records[id][i].StartDate.split('T')[0]) {
                    //         break;
                    //     }
                    // }
                    // if (i < Schedules.records[id].length) {
                    //     // Update existing schedule
                    //     schedule.Id = Schedules.records[id][i].Id;
                    //     Attend.loadAnother();
                    //     $.ajax({
                    //         "url": "api/schedules/" + schedule.Id,
                    //         "method": "put",
                    //         "data": schedule,
                    //
                    //         "dataType": "json",
                    //         "success": function (json) {
                    //             console.log(json);
                    //             Schedules.update(schedule);
                    //             Attend.doneLoading();
                    //         },
                    //         "error": function (xhr) {
                    //             console.log(xhr);
                    //             Attend.doneLoading();
                    //         }
                    //     });
                    // } else {
                    //     // Insert new schedule
                    //     Attend.loadAnother();
                    //     $.ajax({
                    //         "url": "api/schedules",
                    //         "method": "post",
                    //         "data": schedule,
                    //
                    //         "dataType": "json",
                    //         "success": function (json) {
                    //             console.log(json);
                    //             schedule.Id = json.Id;
                    //             Schedules.insert(schedule);
                    //             Attend.doneLoading();
                    //         },
                    //         "error": function (xhr) {
                    //             console.log(xhr);
                    //             Attend.doneLoading();
                    //         }
                    //     });
                    // }
                }
                Attend.doneLoading();
            },
            "error": function (xhr, estring, e) {
                console.log(xhr);
                console.log(estring);
                console.log(e);
                Attend.doneLoading();
                alert("Error");
            }
        });
    }


    return {
        'open': open,
        'close': close
    };
})('#student-props-dlg');


$(async function () {
    await EnrollmentTab.load();
    EnrollmentTab.populate();
    $('#tabs').tabs().show();
});


