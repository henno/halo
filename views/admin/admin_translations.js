// Enable editing table
/* @var $ */
$(function () {
    $("td.editable").dblclick(function () {
        let languageCode = $(this).data('lang');
        let originalContent = $(this).html();
        let translationId = $(this).parents('tr').data('id');
        let translation = prompt("Enter new content for:", originalContent);
        let cell = $(this)

        if (translation != null) {

            // Send value to back-end
            ajax('admin/translationEdit', {translationId, languageCode, translation}, function (res) {
                cell.removeClass('alert-danger');
                cell.addClass('alert-success');
            }, function (res) {
                if (typeof res !== 'undefined') {
                    show_error_modal(res)
                }
                cell.addClass('alert-danger');
            });

            // Write value to table
            $(this).html(translation)
        }
    });
});

// Filter translations table
$('#query').keyup(function () {
    let input, filter, table, tr, td, i;
    input = document.getElementById("query");
    filter = input.value.toUpperCase();
    table = document.getElementById("translations-table");
    tr = table.getElementsByTagName("tr");
    let cell;
    for (i = 2; i < tr.length; i++) {

        // Hide the row initially.
        tr[i].style.display = "none";

        // Get all TDs of the row
        td = tr[i].getElementsByTagName("td");

        for (let j = 0; j < td.length; j++) {
            cell = tr[i].getElementsByTagName("td")[j];
            if (cell) {
                if (cell.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
})