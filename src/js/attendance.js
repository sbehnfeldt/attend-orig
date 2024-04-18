import $ from 'jquery';
import 'jquery-ui/ui/widgets/tabs';
import 'jquery-ui/ui/widgets/dialog';
import 'jquery-ui/ui/widgets/datepicker'
import DataTable from 'datatables.net-dt';
import 'datatables.net-buttons';
import 'datatables.net-select';

'use strict';

let SigninTab = (function (selector) {
    let $tab     = $(selector);
    let $form    = $tab.find('form');
    let $weekOf  = $form.find('input[name=week-of]');
    let $dark    = $form.find('input[type=checkbox]');
    let $options = $form.find('button[name=options]');
    let $pdf     = $form.find('button[name=pdf]');

    $options.on('click', function () {
        $form.find('.options').toggle();
    });
    $pdf.on('click', function () {
        window.location = 'signin.php?week=' + $weekOf.val() + '&' + $dark.serialize();
    });
    $form.on('submit', function () {
        return false;
    });

    return {};

})('#signin-tab');


$(function () {
    console.log('Document loaded.');
    $('table.attendance-table').DataTable({
        'order': [[0, 'asc']],
        'info': false,
        'paging': false,
        'search': false,
        'columnDefs': [
            {targets: [0]},
            {targets: '_all', sortable: false}
        ]
    });

    let $weekOf = $('input[name=week-of]');
    $weekOf.datepicker();
    $('table.attendance-table td').each(function (idx, td) {
        if (!$(this).text().trim()) {
            $(this).addClass('dark');
        }
    });

    $('#tabs').tabs().show();
});
