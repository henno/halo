<style>
    .input-group span {
        width: 200px
    }
</style>
<div class="container">
    <h1>Halo configuration page</h1>

    <p class="help-block">
        This page allows you automatically create a new page which includes
    <ul>
        <li>a database table,</li>
        <li>a controller with 3 actions (index, view, edit)</li>
        <li>views (index, view, edit)</li>
    </ul>

    <? if (!$controllers_folder_is_writable): var_dump(is_writable('fafa')) ?>
        <div class="alert alert-warning">Controller folder is not writable!</div>
    <? endif ?>

    <? if (!$views_folder_is_writable): ?>
        <div class="alert alert-warning">View folder is not writable!</div>
    <? endif ?>


    <? if (!($controllers_folder_is_writable && $views_folder_is_writable)): ?>
        <div class="alert alert-danger">
            <p>Halo doesn't have permission to modify required folders. You can fix this by issuing the following
                command in the project's root folder</p>

            <p>
            <blockquote>chmod a+rwX -R controllers views</blockquote>
            </p></div>
    <? else: ?>
        <h3>New page</h3>

        <p class="help-block">Please input new page name in singular (for database table and view/edit action variables)
            and plural (for controller name and view names):</p>

        <form method="post">

            <div class="input-group">
                <span class="input-group-addon">Name singular</span>
                <input type="text" class="form-control" placeholder="user" name="name_singular">
            </div>
            <div class="input-group">
                <span class="input-group-addon">Name plural</span>
                <input type="text" class="form-control" placeholder="users" name="name_plural">
            </div>
            <p>

            <div class="">
                <button class="btn btn-primary" type="submit">Add</button>
            </div>
        </form>
    <? endif ?>
</div>