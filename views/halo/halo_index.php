<style>
    .input-group span {
        width: 200px
    }

    #divHash {
        padding: 15px;
        display: none;
        border-radius: 5px;
        border: 2px solid gray;
        margin: 10px;
    }

    #txtHash {
        width: 550px;
        border: 0px solid black;
        margin: 16px;

    }

</style>
<div class="container">

    <h1>Generate password</h1>
    <p class="help-block">Type the desired password and click Get hash</p>

    <form action="#">
        <input type="text" id="password">
        <input type="button" id="btnGeneratePasswordHash" value="Get hash">
        <div id="divHash" class="text-center">
            <span id="txtHash" onfocus="this.select()"></span>
            <p class="help-block">Copy the hash and paste it to users.password database field</p>
        </div>
    </form>


    <h1>Page-o-matic</h1>

    <p class="help-block">
        This page allows you automatically create a new page which includes
    <ul>
        <li>a database table,</li>
        <li>a controller with 3 actions (index, view, edit)</li>
        <li>views (index, view, edit)</li>
    </ul>

    <?php if (!$controllers_folder_is_writable): var_dump(is_writable('fafa')) ?>
        <div class="alert alert-warning">Controller folder is not writable!</div>
    <?php endif ?>

    <?php if (!$views_folder_is_writable): ?>
        <div class="alert alert-warning">View folder is not writable!</div>
    <?php endif ?>


    <?php if (!($controllers_folder_is_writable && $views_folder_is_writable)): ?>
        <div class="alert alert-danger">
            <p>Halo doesn't have permission to modify required folders. You can fix this by issuing the following
                command in the project's root folder</p>

            <p>
            <blockquote>chmod a+rwX -R controllers views</blockquote>
            </p></div>
    <?php else: ?>
    <h3>Name</h3>

    <p class="help-block">Please input new page name in singular (for view/edit action variables)
        and plural (for database table name, for controller name and for view names):</p>

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

            <!--

                        <h3>Fields</h3>

                        <p class="help-block">Speficy database fields. Empty display name means that the field won't be displayed in the view</p>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Display name</th>
                                    <th>Database field name</th>
                                    <th>Database field type</th>
                                    <th>Length</th>
                                    <th>Primary?</th>
                                </tr>
                                <tr>
                                    <td><input type="text" name="field[1][name]"/></td>
                                    <td><input type="text" name="field[1][database_name]" value="car_id"/></td>
                                    <td><select name="field[1][database_type]" id="">
                                            <option>TINYINT (1 byte)</option>
                                            <option>SMALLINT (2 bytes)</option>
                                            <option>MEDIUMINT (3 bytes)</option>
                                            <option>INT (4 bytes)</option>
                                            <option>BIGINT (5 bytes)</option>
                                        </select></td>
                                    <td><input type="text" name="field[1][length]"/></td>
                                    <td><input type="checkbox" name="field[1][primary]"/></td>
                                    <td><a href="#">Delete</a></td>
                                </tr>
                                <tr>
                                    <td><input type="text" name="field[2][name]" /></td>
                                    <td><input type="text" name="field[2][database_name]" value="car_name"/></td>
                                    <td><input type="text" name="field[2][type]" /></td>
                                    <td><input type="text" name="field[2][length]"/></td>
                                    <td><input type="checkbox" name="field[1][primary]"/></td>
                                    <td><a href="#">Delete</a></td>
                                </tr>
                            </table>

                            <div class="">
                                <button class="btn btn-default">Add new field</button>
                            </div>
                        </form>

                        <h3>View type</h3>

                        <div class="radio">
                            <label><input type="radio" name="optradio" checked>List</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Table</label>
                        </div>
                -->

        <div class="">
            <button class="btn btn-primary" type="submit">Add</button>
        </div>
        <?php endif ?>
</div>

<script>

    $('#btnGeneratePasswordHash').on('click', function (e) {

        $.post('halo/generate_password_hash', {
            password: $('#password').val()
        }, function (res) {
            $('#txtHash').html(res);
            $('#divHash').slideDown();

        });

        return false;
    });


    $('#txtHash').click(function () {
        SelectText('txtHash');
    });


    function SelectText(element) {
        var doc = document
            , text = doc.getElementById(element)
            , range, selection
        ;
        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }

</script>
