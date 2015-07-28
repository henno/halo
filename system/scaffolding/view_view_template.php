<h1>Module '<?= $module['module_name'] ?>'</h1>
<table class="table table-bordered">

    <tr>
        <th>Module ID</th>
        <td><?= $module['module_id'] ?></td>
    </tr>

    <tr>
        <th>Module name</th>
        <td><?= $module['module_name'] ?></td>
    </tr>

</table>

<!-- EDIT BUTTON -->
<? if ($auth->is_admin): ?>
    <form action="modules/edit/<?= $module['module_id'] ?>">
        <div class="pull-right">
            <button class="btn btn-primary">
                Edit
            </button>
        </div>
    </form>
<? endif; ?>