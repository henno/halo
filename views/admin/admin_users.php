<script src="https://use.fontawesome.com/d37013578f.js"></script>

<style>
    .form-import .file-import {
        display: none;
    }

    .input-box-place {
        padding-bottom: -1px !important;
        margin-bottom: -1px !important;
    }

    .form-group {
        padding: 1px;
        margin: 1px;
    }

    #userName {
        width: 100%;
    }

    #btn-add {
        width: 100%;
    }

    .input-group-append {
        width: 100%;
        padding: 1px !important;
        margin: 1px !important;
    }

</style>

<br>
<div class="input-group mb-3">
    <div class="container">
        <form id="new-user-form">
            <div class="row">
                <div class="col-sm-3 col-md-3 m-0 p-1">
                    <input type="text" class="form-control" name="userName"

                           aria-label="New user's name">
                </div>
                <div class="col-sm-3 col-md-3  m-0 p-1">
                    <input type="email" class="form-control" name="userEmail"

                           aria-label="New user's email">
                </div>
                <div class="col-sm-4 col-md-3  m-0 p-1">
                    <input type="password" class="form-control" name="userPassword"

                           aria-label="New user's password">
                </div>
                <div class="col-sm-2  col-md-3 m-0 p-1">
                    <button class="btn btn-success" id="btn-add"><?= __('Add') ?></button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-sm-3 col-md-3 text-muted">
                <sup><?= __('Name') ?></sup>
            </div>
            <div class="col-sm-3 col-md-3  text-muted">
                <sup><?= __('Email') ?></sup>
            </div>
            <div class="col-sm-4 col-md-3  text-muted">
                <sup><?= __('Password') ?></sup>
            </div>
            <div class="col-sm-2 col-md-3 text-muted">
                <sup></sup>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($users)): ?>
    <table class="table table table-nonfluid table-bordered table-hover table-users bordered">
        <tr>
            <?php foreach ($users[0] as $field => $value): ?>
                <th><?= __(substr($field, 4)) ?></th>
            <?php endforeach ?>
            <th></th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr data-userid="<?= $user['userId'] ?>">
                <?php foreach ($user as $field => $value): ?>
                    <td><?= $value ?></td>
                <?php endforeach ?>
                <td>
                    <a class="edit" data-toggle="modal" data-target=".modal"
                       href="users/edit/<?= $user['userId'] ?>"><i class="fa fa-pencil-square-o"></i></a>&nbsp;
                    <a class="delete" href="users/delete/<?= $user['userId'] ?>"><i
                                class="fa fa-trash-o"></i></a>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>

<div class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('Edit user') ?></h5>
                <button type="button" class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-user-form">
                    <input type="hidden" name="userId"/>
                    <div class="form-group">
                        <label class="mr-sm-2" for="input-user-name"><?= __('Name') ?></label>
                        <input value="" type="text"
                               name="userName"
                               class="form-control"
                               placeholder="<?= __("User's new name") ?>"
                               aria-label="user name">
                    </div>
                    <div class="form-group">
                        <label class="mr-sm-2" for="input-user-name"><?= __('Email') ?></label>
                        <input value="" type="text"
                               name="userEmail"
                               class="form-control"
                               placeholder="<?= __("User's new email") ?>"
                               aria-label="user email">
                    </div>
                    <div class="form-group">
                        <label class="mr-sm-2" for="input-user-name"><?= __('Password') ?></label>
                        <input value="" type="text"
                               name="userPassword"
                               class="form-control"
                               placeholder="<?= __("User's new password (leave empty for unchanged)") ?>"
                               aria-label="user password">
                    </div>
                    <div class="form-group">
                        <label class="mr-sm-2" for="input-user-name"><?= __('Admin') ?></label>
                        <input value="" type="text"
                               name="userIsAdmin"
                               class="form-control"
                               placeholder="<?= __("Set to 1 if user must be admin") ?>"
                               aria-label="user is admin">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-primary btn-save"><?= __('Save changes') ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>

    $('#btn-add').click(function () {
        ajax('admin/addUser', $('#new-user-form').serialize(), RELOAD)
    });

    $('.edit').click(function (e) {

        // Prevent from navigating away from the page
        e.preventDefault()

        // Get selected user's ID
        let userId = $(this).closest('tr').data('userid');

        // Store selected user's ID into form for the back-end
        $('#edit-user-form [name="userId"]').val(userId)

        // Clear password from previous value
        $('#edit-userPassword').val('')

        // Get selected user's data from database
        ajax('admin/getUser', {
            userId
        }, function (res) {

            // Fill modal fields with data from database
            Object.keys(res.data).forEach(function (field) {
                $(`#edit-user-form [name="${field}"]`).val(res.data[field])
            })

        })

    });

    $('.delete').click(function (e) {

        // Prevent from navigating away from the page
        e.preventDefault()

        //Send delete command to server, if user confirms
        if (confirm('<?=__('Are you sure?');?>')) {
            ajax('admin/deleteUser', {
                userId: $(this).closest('tr').data('userid')
            }, RELOAD)
        }

    });

    $('.btn-save').click(function () {

        // Send modal contents to back-end and reload the page to update user's table on the screen
        ajax('admin/editUser', $('#edit-user-form').serialize(), RELOAD)
    });


</script>