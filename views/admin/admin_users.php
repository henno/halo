<script src="https://use.fontawesome.com/d37013578f.js"></script>

<style>
    .form-import .file-import {
        display: none;
    }

    .input-box-place {
        padding-bottom: -1px!important;
        margin-bottom: -1px!important;
    }
    .form-group {
        padding: 1px;
        margin: 1px;
    }

    #input-new-participant-name {
        width: 100%;
    }

    #btn-add {
        width: 100%;
    }

    .input-group-append{
        width: 100%;
        padding: 1px!important;
        margin: 1px!important;
    }

    body > div.container > div.input-group.mb-3.new-event-div > div > div:nth-child(1) > div.col-sm-12.col-md-11.my-0.px-md-0.input-box-place{
        margin-top: 2px!important;
    }
    body > div.container > div.input-group.mb-3.new-event-div > div > div:nth-child(1) > div.col-sm-12.col-md-1.px-md-0{
        padding: 1px!important;
    }


</style>

<br>
<?php if (!empty($selected_county_id)): ?>
    <div class="input-group mb-3 new-event-div">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-11 my-0 px-md-0 input-box-place">
                    <input type="text" class="form-control" id="input-new-participant-name"
                           placeholder="<?= __("New participant's name") ?>" aria-label="New participant's name"
                           aria-describedby="basic-addon2">
                </div>
                <div class="col-sm-12 col-md-1 px-md-0">
                    <div class="input-group-append justify-content-center d-md-table mx-auto form-group">
                        <button class="btn btn-success" id="btn-add"><?= __('Add') ?></button>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-sm-12 col-md-6 px-md-2 text-muted">
                    <sup><?= __('Name') ?></sup>
                </div>
                <div class="col-sm-12 col-md-6 px-md-0 text-center text-muted">
                    <sup></sup>
                </div>
            </div>
        </div>
    </div>
    <form class="form-import float-right" name="import" method="post" action="admin/import_upload"
          enctype="multipart/form-data">
        <input type="button" class="btn btn-success btn-import" value="<?= __('Import') ?>"/>
        <input type="file" name="xlsxFile" class="file-import"
               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
        <input type="hidden" name="county_id" value="<?= $selected_county_id ?>"/>
    </form>
<?php endif ?>
<?php if (!empty($users)): ?>
    <table class="table table table-nonfluid table-bordered table-hover table-users bordered">
        <tr>
            <th><?= __('User') ?></th>
            <?php if (!$selected_county_id): ?><th><?= __('County') ?></th><?php endif ?>
            <th></th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr data-user_id="<?= $user['user_id'] ?>" data-county_id="<?= $user['county_id'] ?>">
                <td><?= $user['name'] ?></td>
                <?php if (!$selected_county_id): ?><td><?= $user['county_name'] ?></td><?php endif ?>
                <td>
                    <a class="edit" data-toggle="modal" data-target=".modal"
                       href="users/edit/<?= $user['user_id'] ?>"><i class="fa fa-pencil-square-o"></i></a>&nbsp;
                    <a class="delete" href="users/delete/<?= $user['user_id'] ?>"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
<div class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('Edit name') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="mr-sm-2" for="input-participant-new-name"><?= __('Name') ?></label>
                    <input value="" type="text" class="form-control" id="input-participant-new-name"
                           placeholder="<?= __("Participant's new name") ?>" aria-label="New participant's name"
                           aria-describedby="basic-addon2">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-save"><?= __('Save changes') ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
    var selectedUserTr;

    $('#btn-add').click(function () {
        ajax('admin/new_user', {
            county_id: <?= empty($selected_county_id) ? 0 : $selected_county_id ?>,
            name: $('#input-new-participant-name').val()
        }, RELOAD)
    });

    $('.edit').click(function (e) {
        selectedUserTr = $(this).closest('tr')
        // Prevent from navigating away from the page
        e.preventDefault()

        // Fill modal text field with selected participant name
        $('#input-participant-new-name').val(selectedUserTr.find('td:nth-child(1)').html())

        // Set modal dropdown value to selected user's county
        $('#county-id').val(selectedUserTr.data('county_id'))

    });

    $('.delete').click(function (e) {
        selectedUserTr = $(this).closest('tr')
        // Prevent from navigating away from the page
        e.preventDefault()

        //Send delete command to server, if user confirms
        if (confirm('<?=__('Are you sure?');?>')) {
            ajax('admin/delete_user', {
                user_id: selectedUserTr.data('user_id')
            }, RELOAD)
        }

    });

    $('.btn-save').click(function () {
        ajax('admin/edit_user', {
            name: $('#input-participant-new-name').val(),
            user_id: selectedUserTr.data('user_id'),
            county_id: $('#county-id').val()
        }, RELOAD)
    });

    $('.btn-import').on('click', function (e) {
        $(this.parentNode).find('.file-import').click();
    });

    $('.file-import').on('change', function (e) {
        $(this.parentNode).submit();
    });

    $('.form-import').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'admin/import_users',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) { //console.log(response);
                alert(response.data)
                location.reload()
            },
            error: function (jqXHR, exception) {
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                $('#post').html(msg);
            },

        });
    });


</script>