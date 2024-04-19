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
    let $self;
    let table;
    let classrooms = [];

    $self = $(selector);
    table = $self.find('table').DataTable({
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
            action: () => ClassroomPropsDlg.clear().populate(table.rows({ selected: true}).data()[0]).open()
        }]
    });


    // let b0 = new $.fn.dataTable.Buttons(table, {
    //     buttons: [{
    //         "text": "New",
    //         "action": function () {
    //             ClassroomPropsDlg.open();
    //         }
    //     }, {
    //         "extend": "selected",
    //         "text": "Edit",
    //         "action": function (e, dt, button, config) {
    //             let selected = dt.rows({selected: true}).indexes();
    //             if (1 < selected.length) {
    //                 alert("Can edit only 1 record at a time");
    //             } else {
    //                 ClassroomPropsDlg.open(dt.rows(selected[0]).data()[0]);
    //             }
    //         }
    //     }, {
    //         "extend": "selected",
    //         "text": "Delete",
    //         "action": function (e, dt) {
    //             let selected = dt.rows({selected: true});
    //             let msg      = (1 === selected[0].length) ? 'Are you sure you want to delete this record?' : 'Are you sure you want to delete these ' + selected[0].length + ' records?';
    //             if (confirm(msg)) {
    //                 let length = selected[0].length;
    //                 selected.every(function () {
    //                     let row  = this;
    //                     let data = row.data();
    //                     Attend.loadAnother();
    //                     $.ajax({
    //                         "url": "api/classrooms/" + data.Id,
    //                         "method": "delete",
    //
    //                         "success": function (json) {
    //                             length--;
    //                             if (!length) {
    //                                 selected.remove().draw(false);
    //                             }
    //                             Attend.doneLoading();
    //                         },
    //                         "error": function (xhr) {
    //                             console.log(xhr);
    //                             length--;
    //                             row.deselect();
    //                             selected = dt.rows({selected: true});
    //                             if (!length) {
    //                                 selected.remove().draw(false);
    //                             }
    //                             Attend.doneLoading();
    //                         }
    //                     });
    //                 });
    //             }
    //         }
    //     }]
    // });
    // b0.dom.container.eq(0).appendTo($self.find('.record-buttons'));
    //
    // let b1 = new $.fn.dataTable.Buttons(table, {
    //     "buttons": [{
    //         "text": "Reload",
    //         "action": function (e, dt) {
    //             Attend.loadAnother();
    //             table.clear();
    //             dt.ajax.reload(Attend.doneLoading);
    //         }
    //     }]
    // });
    // b1.dom.container.eq(0).appendTo($self.find('.table-buttons span'));


    function insert(data) {
        table.row.add(data).draw();
    }


    async function load() {
        Attend.loadAnother();
        classrooms = await AttendApi.classrooms.select();
        Attend.doneLoading();
    }

    function populate() {
        table.clear();
        for (let i = 0; i < classrooms.length; i++ ) {
            table.row.add(classrooms[i]);
        }
        table.draw();
        return this;
    }

    // function reload() {
    //     table.ajax.reload();
    // }

    function redrawRow(newData) {
        table.rows().every(function ( /* rowIdx, tableLoop, rowLoop */) {
            let data = this.data();
            if (data.Id == newData.Id) {
                let oldData = this.data();
                for (let p in newData) {
                    oldData[p] = newData[p];
                }
                this.data(oldData);
            }
        });
    }

    function deleteRow(classroom_id) {
        table.rows().every(function (rowIdx, tableLoop, rowLoop) {
            let data = this.data();
            console.log(rowIdx);
            console.log(tableLoop);
            console.log(rowLoop);

            console.log(data);
            if (classroom_id == data.id) {
                this.remove();
            }
        });
    }

    return { load, populate, insert, redrawRow, deleteRow };
})('#classrooms-tab');

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
            "Submit": function () {
                if (validate()) {
                    submit();
                    close();
                }
            },
            "Cancel": function () {
                ClassroomPropsDlg.close();
            }
        }
    });

    function open(classroom) {
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
        console.log(classroom);
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

    function submit() {
        let id       = $classroomId.val();
        let label    = $label.val();
        let ordering = $order.val();
        if (ordering === '') {
            ordering = null;
        }
        let data = {
            "Label": label,
            "Ordering": ordering
        };
        if (!id) {
            insert(data);
        } else {
            update(id, data);
        }
        ClassroomPropsDlg.close();
    }

    function insert(data) {
        Attend.loadAnother();
        $.ajax({
            "url": "api/classrooms",
            "method": "post",
            "data": data,

            "dataType": "json",
            "success": function (json) {
                console.log(json);
                if (!data.ordering) {
                    // If ordering not specified, it defaults to current max + 1,
                    // so table is fine; just add new row
                    ClassroomsTab.insert(json);
                } else {
                    // If ordering IS specified, ordering of other classrooms may be affected;
                    // so, reload entire table.
                    ClassroomsTab.reload(json);
                }
                Attend.doneLoading();

//                    $.ajax( {
//                        'url'   : "api/classrooms/" + json,
//                        "method": "get",
//
//                        "success": function ( json ) {
//                            console.log( json );
//                            if ( !data.ordering ) {
//                                // If ordering not specified, it defaults to current max + 1,
//                                // so table is fine; just add new row
//                                ClassroomsTab.insert( json );
//                            } else {
//                                // If ordering IS specified, ordering of other classrooms may be affected;
//                                // so, reload entire table.
//                                ClassroomsTab.reload( json );
//                            }
//                            Attend.doneLoading();
//                        },
//                        "error"  : function ( xhr ) {
//                            console.log( xhr );
//                            Attend.doneLoading();
//                        }
//                    } );

            },
            "error": function (xhr) {
                console.log(xhr);
                Attend.doneLoading();
            }
        });

    }

    function update(id, data) {
        Attend.loadAnother();
        $.ajax({
            "url": "api/classrooms/" + id,
            "method": "put",
            "data": data,

            "dataType": "json",
            "success": function (json) {
                console.log(json);
                ClassroomsTab.redrawRow(json);
                Attend.doneLoading();
            },
            "error": function (xhr) {
                console.log(xhr);
                Attend.doneLoading();
            }
        });
    }


    return { clear, populate, open, close };
})('#classroom-props-dlg');

$(async function () {
    await ClassroomsTab.load();
    ClassroomsTab.populate();
    $('#tabs').tabs().show();
});

