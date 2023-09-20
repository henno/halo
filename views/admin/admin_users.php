<script src="https://use.fontawesome.com/d37013578f.js"></script>
<style>
    #btn-add {
        height: 38px;
        width: 100%;
    }

    /* Remove margin-left: 0 from form when in mobile mode */
    @media (max-width: 767px) {
        #new-user-form {
            margin-left: auto;
        }
    }

    @media only screen and (max-width: 767.98px) {
        body .ui:not(.segment):not(.grid) .ui.stackable.grid, body > .ui.stackable.grid {
            margin-left: -15px !important;  /* Or whatever value you need */
            margin-right: -15px !important;  /* Or whatever value you need */
        }
    }
</style>

<form class="ui form" id="new-user-form">
    <div class="ui stackable four column grid">
        <div class="column">
            <div class="fluid input">
                <label for="userName"><?= __('Name') ?></label>
                <input type="text" name="userName" id="userName" placeholder="John Doe">
            </div>
        </div>
        <div class="column">
            <div class="fluid input">
                <label for="userEmail"><?= __('Email') ?></label>
                <input type="text" name="userEmail" id="userEmail" placeholder="john@example.com">
            </div>
        </div>
        <div class="column">
            <div class="fluid input">
                <label for="userPassword"><?= __('Password') ?></label>
                <input type="password" name="userPassword" id="userPassword" placeholder="Secret">
            </div>
        </div>
        <div class="column bottom aligned">
            <button class="ui green button" id="btn-add" type="button">Add</button>
        </div>
    </div>
</form>

<?php if (!empty($users)): ?>
    <table class="ui celled table unstackable table-users">
        <thead>
        <tr>
            <?php foreach ($users[0] as $field => $value): ?>
                <th><?= __(substr($field, 4)) ?></th>
            <?php endforeach ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>
<?php endif ?>

<div class="ui modal">
    <i class="close icon"></i>
    <div class="header">
        <?= __('Edit user') ?>
    </div>
    <div class="content">
        <form id="edit-user-form" class="ui form">
            <input type="hidden" name="userId"/>
            <div class="field">
                <label for="userName"><?= __('Name') ?></label>
                <input type="text" name="userName" id="userName" placeholder="<?= __("User's new name") ?>">
            </div>
            <div class="field">
                <label for="userEmail"><?= __('Email') ?></label>
                <input type="email" name="userEmail" id="userEmail" placeholder="<?= __("User's new email") ?>">
            </div>
            <div class="field">
                <label for="userPassword"><?= __('Password') ?></label>
                <input type="password" name="userPassword" id="userPassword"
                       placeholder="<?= __("User's new password (leave empty for unchanged)") ?>">
            </div>
            <div class="field">
                <label for="userIsAdmin"><?= __('Admin') ?></label>
                <input type="text" name="userIsAdmin" id="userIsAdmin"
                       placeholder="<?= __("Set to 1 if user must be admin") ?>">
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

        ajax('admin/getUser', {userId}, function (res) { // Get selected user's data from the database
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
    $(document).on('click', '.ui.secondary.button', function () {
        $('.ui.modal').modal('hide');
    });

</script>