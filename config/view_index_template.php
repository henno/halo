<h3>module</h3>
<ul class="list-group">
    <? foreach ($modules as $module): ?>
        <li class="list-group-item">
            <a href="modules/view/<?= $module['module_id'] ?>/<?= $module['module_name'] ?>"><?= $module['module_name'] ?></a>
        </li>
    <? endforeach ?>
</ul>

<?php if ($auth->is_admin): ?>
<h3>Add new module</h3>

<form method="post" id="form">
    <form id="form" method="post">
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td><input type="text" name="data[module_name]" placeholder=""/></td>
            </tr>
        </table>

        <button class="btn btn-primary" type="submit">Add</button>
    </form>
    <?php endif; ?>
