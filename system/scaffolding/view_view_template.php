<style>
    th {
        text-align: center;
        background-color: #f5f5f5 !important;
    }
</style>
<div class="row">

    <h1>Modules</h1>

    <div class="table-responsive">

        <table class="ui celled table table-striped table-bordered">


            <tbody>

            <tr>
                <th>ID</th>
                <td><?= $module['moduleId'] ?></td>
            </tr>
            <tr>
                <th><?= __('Module Name') ?></th>
                <td><?= $module['moduleName'] ?></td>
            </tr>

            </tbody>

        </table>

    </div>
</div>
