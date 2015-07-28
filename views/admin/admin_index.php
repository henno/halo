<div class="container">
    <h1>Halo configuration page</h1>

    <p class="help-block">
        This page allows you automatically create a new controller which includes
    <ul>
        <li>a database table,</li>
        <li>a controller with 3 actions (index, view, edit)</li>
        <li>views (index, view, edit)</li>
    </ul>
    </p>
    <h3>New controller</h3>

    <p class="help-block">Please input new controller name in singular and plural:</p>

    <form method="post">

        <div class="input-group">
            <span class="input-group-addon">Controller name singular</span>
            <input type="text" class="form-control" placeholder="user" name="name_singular">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Controller name plural</span>
            <input type="text" class="form-control" placeholder="users" name="name_plural">
        </div>
        <p>

        <div class="pull-right">
            <button class="btn btn-primary" type="submit">Add</button>
        </div>
        </p>
    </form>

</div>