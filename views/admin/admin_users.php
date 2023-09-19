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
<div class="ui modal">
    <i class="close icon"></i>
    <div class="header">
        <?= __('Edit user') ?>
    </div>
    <div class="content">
        <form id="edit-user-form" class="ui form">
            <input type="hidden" name="userId" />
            <div class="field">
                <label><?= __('Name') ?></label>
                <input type="text" name="userName" placeholder="<?= __("User's new name") ?>">
            </div>
            <div class="field">
                <label><?= __('Email') ?></label>
                <input type="email" name="userEmail" placeholder="<?= __("User's new email") ?>">
            </div>
            <div class="field">
                <label><?= __('Password') ?></label>
                <input type="password" name="userPassword" placeholder="<?= __("User's new password (leave empty for unchanged)") ?>">
            </div>
            <div class="field">
                <label><?= __('Admin') ?></label>
                <input type="text" name="userIsAdmin" placeholder="<?= __("Set to 1 if user must be admin") ?>">
            </div>
        </form>
    </div>
    <div class="actions">
        <button class="ui primary button btn-save"><?= __('Save changes') ?></button>
        <button class="ui secondary button btn-close"><?= __('Close') ?></button>
    </div>
</div>


<script>

    // Add User
    $('#btn-add').click(function () {
        ajax('admin/addUser', $('#new-user-form').serialize(), RELOAD)
    });

    // Edit User
    $('.edit').click(function (e) {
        e.preventDefault(); // Prevent from navigating away from the page

        let userId = $(this).closest('tr').data('userid'); // Get selected user's ID

        $('#edit-user-form [name="userId"]').val(userId); // Store selected user's ID into the form for the back-end

        $('#edit-user-form [name="userPassword"]').val(''); // Clear password from previous value

        ajax('admin/getUser', { userId }, function (res) { // Get selected user's data from the database
            Object.keys(res.data).forEach(function (field) { // Fill modal fields with data from the database
                $(`#edit-user-form [name="${field}"]`).val(res.data[field]);
            });

            $('.ui.modal').modal('show'); // Open the modal manually using Semantic UI's modal component
        });
    });

    // Delete User
    $('.delete').click(function (e) {
        e.preventDefault(); // Prevent from navigating away from the page

        if (confirm('<?=__('Are you sure?');?>')) { // Send delete command to the server, if the user confirms
            ajax('admin/deleteUser', {
                userId: $(this).closest('tr').data('userid')
            }, RELOAD);
        }
    });

    // Save Changes in the Modal
    $('.btn-save').click(function () {
        ajax('admin/editUser', $('#edit-user-form').serialize(), RELOAD); // Send modal contents to the back-end and reload the page
    });
    // This ensures the close button will close the modal
    $(document).on('click', '.ui.secondary.button', function() {
        $('.ui.modal').modal('hide');
    });

</script>