import $ from 'jquery';
import 'jquery-ui/ui/widgets/tabs';
import 'jquery-ui/ui/widgets/dialog';
import 'jquery-ui/ui/widgets/datepicker'
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons';
import 'datatables.net-select';
import Attend from "./attend";
import AttendApi from './attend-api';
import moment from "moment";

'use strict';


let EnrollmentTab = (function (selector) {
    let classrooms = [];
    let students   = [];
    let schedules  = [];

    let table = new DataTable(selector, {
        data: students,
        layout: {
            top1Start: 'info',
            top2Start: null,
            topStart: "pageLength",
            topEnd: 'search',
            bottomStart: 'buttons',
            bottomEnd: 'paging'
        },

        order: [[1, 'asc']],
        select: "single",
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
                    let classroom = classrooms.find((e) => e.Id === data);
                    return classroom.Label;
                }
            }
        ],
        buttons: [{
            text: 'New',
            action: () => StudentPropsDlg.clear().open() // Clear and open the "Student Properties" dialog
        }, {
            text: 'Update',
            extend: 'selected',
            // Populate (with data from the selected row) and open the "Student Properties" dialog
            action: () => {
                StudentPropsDlg.clear().populate(table.rows({selected: true}).data()[0]).open()
            }
        }, {
            text: 'Delete',
            extend: 'selected',
            action: async () => {
                await remove(table.rows({selected: true}).data()[0].Id);
                await load();
                populate();
            }
        }]
    });

    function load() {
        Attend.loadAnother();
        try {
            return Promise.all([AttendApi.classrooms.select(), AttendApi.students.select(), AttendApi.schedules.select()])
                .then((values) => {
                    classrooms = values[0];
                    students   = values[1];
                    schedules  = values[2];

                    // Update each object in the "students" array with the corresponding classroom and schedule data.
                    // This will eliminate the need to search through the classrooms and schedules arrays later on.
                    students.forEach((elem, idx, arr) => {
                        elem['Classroom'] = classrooms.find((e) => e.Id === elem.ClassroomId);
                        elem['Schedules'] = schedules
                            .filter((e) => e.StudentId === elem.Id)
                            .sort((a, b) => {
                                if (moment(a.StartDate) < moment(b.StartDate)) {
                                    return 1;
                                } else if (moment(a.StartDate) > moment(b.StartDate)) {
                                    return -1;
                                }
                                return 0;
                            });
                    });

                    Attend.doneLoading();
                })
                .catch((err) => {
                    console.error(err);
                    alert("Students didn't load");
                    Attend.doneLoading();
                });
        } catch (e) {
            console.log(e);
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

    async function remove(id) {
        Attend.loadAnother();
        await AttendApi.students.remove(id);
        Attend.doneLoading();
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
        autoOpen: false,
        modal: true,
        width: "450px",
        buttons: {
            Submit: function onSubmit() {
                if (validate()) {
                    submit();
                    close();
                }
            },
            Cancel: function onCancel() {
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


    // When the drop-down list of defined schedules changes,
    // populate the schedule check boxes
    $list.on('change', function () {
        let sched = this.value;
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


    function open() {
        $startDate.datepicker('setDate', Attend.getMonday(new Date()));
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
        return this;
    }

    function populate(student) {

        $studentId.val(student.Id);
        $familyName.val(student.FamilyName);
        $firstName.val(student.FirstName);
        $classrooms.val(student.ClassroomId);
        $enrolled.prop('checked', (1 == student.Enrolled));
        $startDate.datepicker('setDate', Attend.getMonday(new Date()));

        $list.removeClass('hidden');
        for (let i = 0; i < student.Schedules.length; i++) {
            let sched = student.Schedules[i];
            let $opt  = $('<option>').text(sched.StartDate.split('T')[0]).val(sched.Schedule);
            $list.append($opt);
        }
        $list.trigger('change');
        return this;
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


    return {clear, populate, open, close};
})('#student-props-dlg');


$(async function () {
    await EnrollmentTab.load();
    EnrollmentTab.populate();
});
