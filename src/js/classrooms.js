import $ from 'jquery';
import 'jquery-ui/ui/widgets/tabs';
import 'jquery-ui/ui/widgets/dialog';
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons';
import 'datatables.net-select';
import moment from 'moment';
import Attend from "./attend";
import AttendApi from "./attend-api";

'use strict';

let ClassroomsTab = (function (selector) {
    let table;
    let classrooms = [];

    table = new DataTable(selector, {
        data: classrooms,
        layout: {
            top1Start: 'info',
            topStart: "pageLength",
            topEnd: 'search',
            bottomStart: 'buttons',
            bottomEnd: 'paging'
        },
        autoWidth: false,
        paging: false,
        searching: false,
        select: "single",
        order: [[2, "asc"]],
        columns: [
            {data: "Id"},
            {data: "Label"},
            {data: "Ordering"}, {
                data: "CreatedAt",
                render: (x) => {
                    return moment(x).format('YYYY-MM-DD');
                }
            }, {
                data: "UpdatedAt",
                render: (x) => {
                    return x ? moment(x).format('YYYY-MM-DD') : '';
                }
            }
        ],
        buttons: [{
            text: 'New',
            action: () => ClassroomPropsDlg.clear().open() // Clear and open the "Classroom Properties" dialog
        }, {
            text: 'Update',
            extend: 'selected',
            // Populate (with data from the selected row) and open the "Classroom Properties" dialog
            action: () => ClassroomPropsDlg.clear().populate(table.rows({selected: true}).data()[0]).open()
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

    async function load() {
        Attend.loadAnother();
        classrooms = await AttendApi.classrooms.select();
        Attend.doneLoading();
    }

    function populate() {
        table.clear();
        for (let i = 0; i < classrooms.length; i++) {
            table.row.add(classrooms[i]);
        }
        table.draw();
        return this;
    }

    async function remove(id) {
        Attend.loadAnother();
        await AttendApi.classrooms.remove(id);
        Attend.doneLoading();
    }

    return {load, populate };
})('#classrooms-table');


let ClassroomPropsDlg = (function (selector) {
    let $self,
        $form,
        $classroomId,
        $label,
        $order,
        $inputs,
        $required,
        dialog;

    $self        = $(selector);
    $form        = $self.find('form');
    $classroomId = $form.find('[name=Id]');
    $label       = $form.find('[name=Label]');
    $order       = $form.find('[name=Ordering]');


    $inputs = $form.find('input');
    $inputs.on('change', function () {
        if ($(this).val() !== $(this).data('db-val')) {
            $(this).addClass('modified');
        } else {
            $(this).removeClass('modified');
        }
    });

    $required = $form.find('.required');

    dialog = $self.dialog({
        "autoOpen": false,
        "modal": true,
        "width": "300px",
        "buttons": {
            "Submit": async function () {
                if (validate()) {
                    await submit();
                    await ClassroomsTab.load().then(() => ClassroomsTab.populate());
                }
            },
            "Cancel": function () {
                ClassroomPropsDlg.close();
            }
        }
    });


    function open() {
        dialog.dialog('open');
    }


    function close() {
        dialog.dialog('close');
    }


    function clear() {
        $form[0].reset();
        $required.removeClass('missing');
        $inputs.data('db-val', '').removeClass('modified');
        return this;
    }


    function populate(classroom) {
        $classroomId.val(classroom.Id);
        $label.val(classroom.Label).data('db-val', classroom.Label);
        $order.val(classroom.Ordering).data('db-val', classroom.Ordering);
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
        let data = {
            Id: '' === $classroomId.val() ? null : $classroomId.val(),
            Label: $label.val(),
            Ordering: '' === $order.val() ? null : $order.val()
        };
        if ($classroomId.val()) {
            await AttendApi.classrooms.update(data);
        } else {
            await AttendApi.classrooms.insert(data);
        }
        close();
    }

    return {clear, populate, open, close};
})('#classroom-props-dlg');

$(async function () {
    await ClassroomsTab.load();
    ClassroomsTab.populate();
    $('#tabs').tabs().show();
});

