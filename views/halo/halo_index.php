<style>
    .input-group span {
        width: 200px
    }

    #divHash {
        padding: 15px;
        display: none;
        border-radius: 5px;
        border: 2px solid rgb(128, 128, 128);
        margin: 10px;
    }

    #txtHash {
        width: 550px;
        border: 0px solid black;
        margin: 16px;

    }

    #success-message, #error-message {
        display: none;
    }

</style>
<div class="container">
    <div class="alert alert-success" id="success-message"></div>
    <div class="alert alert-danger" id="error-message"></div>
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
            <input type="text" class="form-control" placeholder="user" id="name_singular">
        </div>
        <div class="input-group">
            <span class="input-group-addon">Name plural</span>
            <input type="text" class="form-control" placeholder="users" id="name_plural">
        </div>
        <p>

        <div class="">
            <button class="btn btn-primary" id="btn-add">Add</button>
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

    $('#btn-add').click(function (e) {
        e.preventDefault();
        ajax('halo/create_module', {
            name_plural: $('#name_plural').val(),
            name_singular: $('#name_singular').val()
        }, (res) => {
            $('#success-message').html(res.data);
            $('#success-message').fadeIn();

        },(err) => {
            $('#error-message').html(err);
            $('#error-message').fadeIn();
        })
    })

</script>
