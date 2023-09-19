<script src="https://use.fontawesome.com/d37013578f.js"></script>

<style>
    .form-container {
        margin-top: 5rem;
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 0.5em;
    }

    #btn-add {
        width: 20%;
        /*margin-top: 1em;*/
    }

    .ui.grid > .row {
        margin: 0.5rem !important; /* Remove the row margin */
        padding: 0.1em !important; /* Add a little padding */
    }

    .small-form .ui.labeled.input {
        font-size: 0.8em;  /* Adjusts the text size */
        width: 30%;        /* Adjusts the width */
    }

    .small-form input {
        height: 2.8em;  /* Adjusts the height */
    }

    .form-control{
        width: 20%;
    }
</style>

<div class="ui container form-container small-form">
    <form id="new-user-form" class="ui form">
        <div class="ui grid">
            <!-- Name Field -->
            <div class="one column row">
                <div class="column">
                    <div class="ui labeled input form-group">
                        <div class="ui label">
                            <?= __('Name') ?>
                        </div>
                        <input type="text" name="userName" aria-label="New user's name">
                    </div>
                </div>
            </div>
            <!-- Email Field -->
            <div class="one column row">
                <div class="column">
                    <div class="ui labeled input form-group">
                        <div class="ui label">
                            <?= __('Email') ?>
                        </div>
                        <input type="email" name="userEmail" aria-label="New user's email">
                    </div>
                </div>
            </div>
            <!-- Password Field -->
            <div class="one column row">
                <div class="column">
                    <div class="ui labeled input form-group">
                        <div class="ui label">
                            <?= __('Password') ?>
                        </div>
                        <input type="password" name="userPassword" aria-label="New user's password">
                    </div>
                </div>
            </div>
            <!-- Add Button -->
            <div class="one column row">
                <div class="column">
                    <button class="ui green button form-group" id="btn-add"><?= __('Add') ?></button>
                </div>
            </div>
        </div>
    </form>
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

<div class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><?= __('Edit user') ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-user-form" class="form">
                    <input type="hidden" name="userId">

                    <!-- Form groups -->
                    <div class="form-group">
                        <label for="input-user-name"><?= __('Name') ?></label>
                        <input value="" type="text" name="userName" class="form-control" placeholder="<?= __("User's new name") ?>" aria-label="user name">
                    </div>

                    <div class="form-group">
                        <label for="input-user-email"><?= __('Email') ?></label>
                        <input value="" type="text" name="userEmail" class="form-control" placeholder="<?= __("User's new email") ?>" aria-label="user email">
                    </div>

                    <div class="form-group">
                        <label for="input-user-password"><?= __('Password') ?></label>
                        <input value="" type="password" name="userPassword" class="form-control" placeholder="<?= __("User's new password (leave empty for unchanged)") ?>" aria-label="user password">
                    </div>

                    <div class="form-group">
                        <label for="input-user-isAdmin"><?= __('Admin') ?></label>
                        <input value="" type="text" name="userIsAdmin" class="form-control" placeholder="<?= __("Set to 1 if user must be admin") ?>" aria-label="user is admin">
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-save"><?= __('Save changes') ?></button>
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