<h1>Module '<?= $module['module_name'] ?>'</h1>
<table class="table table-bordered">
    <tr>
        <th>modulename</th>
        <td><?= $module['modulename'] ?></td>
    </tr>
    <? if( $auth->is_admin ): ?>
        <tr>
            <th>Password</th>
            <td><?= $module['password'] ?></td>
        </tr>
    <? endif; ?>
    <tr>
        <th>Active</th>
        <td><input type="checkbox" name="data[active]" <?= $module['active'] != 0 ? 'checked="checked"' : '' ?> disabled="disabled"/></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= $module['email'] ?></td>
    </tr>
</table>

<!-- EDIT BUTTON -->
<? if($auth->is_admin):?>
    <form action="modules/edit/<?= $module['module_id'] ?>">
        <div class="pull-right">
            <button class="btn btn-primary">
                Edit
            </button>
        </div>
    </form>
<? endif; ?>