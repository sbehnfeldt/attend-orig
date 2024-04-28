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
                    students.forEach((student, idx, arr) => {
                        student['Classroom'] = classrooms.find((classroom) => classroom.Id === student.ClassroomId);
                        student['Schedules'] = schedules
                            .filter((e) => e.StudentId === student.Id)
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
        try {
            await AttendApi.students.remove(id);
        } catch (e) {
            console.log(e);
            alert( "Unable to delete student." );
        } finally {
            Attend.doneLoading();
        }
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

    // Fill in the Student Properties form
    function populate(student) {

        $studentId.val(student.Id);
        $familyName.val(student.FamilyName);
        $firstName.val(student.FirstName);
        $classrooms.val(student.ClassroomId);
        $enrolled.prop('checked', (1 == student.Enrolled));
        $startDate.datepicker('setDate', Attend.getMonday(new Date()));

        // Populate the drop-down list of the student's (previously-defined) schedules
        $list.removeClass('hidden');
        for (let i = 0; i < student.Schedules.length; i++) {
            let sched = student.Schedules[i];
            let $opt  = $('<option>')
                .text(sched.StartDate.split('T')[0])
                .val(sched.Schedule)
                .data('id', sched[ 'Id' ])
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

    async function submit() {
        let student, schedule;

        // Student data (from the form)
        student = {
            Id: '' === $studentId.val() ? null : $studentId.val(),
            FamilyName: $familyName.val(),
            FirstName: $firstName.val(),
            Enrolled: (true === $enrolled.prop('checked')) ? 1 : 0,
            ClassroomId: $classrooms.val() ? $classrooms.val() : null
        };

        // Schedule data (from the form)
        schedule = 0;
        $boxes.each(function (i, e) {
            if ($(e).prop('checked')) {
                schedule += parseInt($(e).val(), 16);
            }
        });

        Attend.loadAnother();
        try {
            if (!$studentId.val()) {
                // New student: insert student, then insert schedule (need to wait for ID of new student before inserting schedule)
                student = await AttendApi.students.insert(student);
                await AttendApi.schedules.insert({
                    StudentId: student.Id,
                    Schedule: schedule,
                    StartDate: moment()
                });

            } else {
                // Current student: update student and schedule (if necessary)
                student = await AttendApi.students.update(student);

                let cur = $list.find('option:eq(0)').val();

                // See if new schedule is different from the current schedule
                if (parseInt(cur) !== schedule) {
                    // Inserting a new schedule

                    let now = moment();

                    if ( now.format('Y-MM-DD') !== $list.find('option:eq(0)').text()) {

                        await AttendApi.schedules.insert({
                            StudentId: student.Id,
                            Schedule: schedule,
                            StartDate: moment()
                        });
                    } else {
                        await AttendApi.schedules.update({
                            Id: $list.find('option:eq(0)').data('id'),
                            StudentId: student.Id,
                            Schedule: schedule,
                            StartDate: moment()
                        });
                    }
                }
            }

            await EnrollmentTab.load();
            EnrollmentTab.populate();

        } catch (e) {
            console.log(e);
            alert("Unable to complete operation");
        } finally {
            Attend.doneLoading();
        }



        StudentPropsDlg.close();
    }



    return {clear, populate, open, close};
})('#student-props-dlg');


$(async function () {
    await EnrollmentTab.load();
    EnrollmentTab.populate();
});
