<div class="row">

    <h1>Modules</h1>

    <div class="table-responsive">
        <!-- Fomantic UI table -->
        <table class="ui celled table table-striped table-bordered">

            <thead>

            <tr>
                <th>ID</th>
                <th><?= __('Module Name') ?></th>
            </tr>

            </thead>

            <tbody>

            <?php foreach ($modules as $module): ?>
                <tr data-href="modules/<?= $module['module_id'] ?>">
                    <td><?= $module['module_id'] ?></td>
                    <td><?= $module['module_name'] ?></td>
                </tr>
            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>
