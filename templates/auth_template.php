<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <title><?= PROJECT_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" href="vendor/components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/components/bootstrap/css/bootstrap-theme.min.css">
    <script src="vendor/components/jquery/jquery.min.js"></script>
    <script src="vendor/components/bootstrap/js/bootstrap.min.js"></script>

    <style>
        body {
            padding-top: 50px;
        }

        .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }

        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }

        .form-signin .checkbox {
            font-weight: normal;
        }

        .form-signin .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .form-signin .form-control:focus {
            z-index: 2;
        }

        .modal-input input[type="text"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .modal-input input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        span.input-group-addon {
            width: 50px;
        }

        div.input-group {
            width: 100%;
        }

        form.form-signin {
            background-color: #ffffff;
        }
    </style>
</head>

<body>

<div class="container">

    <form class="form-signin" method="post">

        <h2 class="form-signin-heading"><?= __('Please sign in') ?></h2>

        <?php if (isset($errors)) {
            foreach ($errors as $error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endforeach;
        } ?>


        <label for="user"><?= __('Username') ?></label>

        <div class="input-group">
            <span class="input-group-addon"><i class="icon-user"></i></span>
            <input id="user" name="email" type="text" class="form-control" placeholder="email" autofocus>
        </div>

        <br/>

        <label for="pass"><?= __('Password') ?></label>

        <div class="input-group">
            <span class="input-group-addon"><i class="icon-key"></i></span>
            <input id="pass" name="password" type="password" class="form-control" placeholder="******">
        </div>

        <br/>

        <button class="btn btn-lg btn-primary btn-block" type="submit"><?= __('Sign in') ?></button>
    </form>

</div>
<!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
</body>
</html>
