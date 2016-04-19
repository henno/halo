<h1><?= __("Module") ?> '<?= $module['module_name'] ?>'</h1>
<table class="table table-bordered">

    <tr>
        <th><?= __("Module") ?> ID</th>
        <td><?= $module['module_id'] ?></td>
    </tr>

    <tr>
        <th><?= __("Module") ?><?= __("name") ?></th>
        <td><?= $module['module_name'] ?></td>
    </tr>

</table>

<!-- EDIT BUTTON -->
<?php if ($auth->is_admin): ?>
    <form action="modules/edit/<?= $module['module_id'] ?>">
        <div class="pull-right">
            <button class="btn btn-primary">
                <?= __("Edit") ?>
            </button>
        </div>
    </form>
<?php endif; ?>