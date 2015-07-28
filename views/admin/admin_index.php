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
    </p>
    <h3>New page</h3>

    <p class="help-block">Please input new page name in singular (for database table and view/edit action variables) and plural (for controller name and view names):</p>

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
        </p>
    </form>

</div>