<?php if (!$auth->is_admin): ?>
    <div class="alert alert-danger fade in">
        <button class="close" data-dismiss="alert">×</button>
        You are not an administrator.
    </div>
    <?php exit(); endif; ?>
<h1>Module '<?= $module['module_name'] ?>'</h1>
<form id="form" method="post">
    <table class="table table-bordered">
        <tr>
            <th><?= __('Module name') ?></th>
            <td><input type="text" name="data[module_name]" value="<?= $module['module_name'] ?>"/></td>
        </tr>
    </table>
</form>

<!-- BUTTONS -->
<div class="pull-right">

    <!-- CANCEL -->
    <button class="btn btn-default"
            onclick="window.location.href = 'modules/view/<?= $module['module_id'] ?>/<?= $module['modulename'] ?>'">
        <?= __("Cancel") ?>
    </button>

    <!-- DELETE -->
    <button class="btn btn-danger" onclick="delete_module(<?= $module['module_id'] ?>)">
        <?= __("Delete") ?>
    </button>

    <!-- SAVE -->
    <button class="btn btn-primary" onclick="$('#form').submit()">
        <?= __("Save") ?>
    </button>

</div>
<!-- END BUTTONS -->

<!-- JAVASCRIPT
==============================================================================-->
<script type="application/javascript">
    function delete_module() {
        $.post('<?=BASE_URL?>modules/delete', {module_id: <?= $module['module_id'] ?>}, function (response) {
            if(response == 'Ok'){
                window.location.href = '<?=BASE_URL?>modules';
            }
        })
    }
</script>