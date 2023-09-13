<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <title><?= PROJECT_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

    <!-- Fomantic core CSS -->
    <link href="node_modules/fomantic-ui-css/semantic.min.css?<?= COMMIT_HASH ?>" rel="stylesheet">
    <!-- Site core CSS -->
    <link href="assets/css/main.css?<?= COMMIT_HASH ?>" rel="stylesheet">

    <!-- jQuery -->
    <script src="node_modules/jquery/dist/jquery.min.js?<?= COMMIT_HASH ?>"></script>

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

<div class="ui container">

    <form class="ui form segment" method="post">

        <h2 class="ui header"><?= __('Please sign in') ?></h2>

        <?php if (isset($errors)) {
            foreach ($errors as $error): ?>
                <div class="ui negative message">
                    <?= $error ?>
                </div>
            <?php endforeach;
        } ?>

        <div class="field">
            <label for="user"><?= __('Email') ?></label>
            <div class="ui left icon input">
                <input id="user" name="userEmail" type="text" placeholder="demo@example.com" autofocus>
                <i class="user icon"></i>
            </div>
        </div>

        <div class="field">
            <label for="pass"><?= __('Password') ?></label>
            <div class="ui left icon input">
                <input id="pass" name="userPassword" type="password" placeholder="******">
                <i class="lock icon"></i>
            </div>
        </div>

        <button class="ui blue button" type="submit"><?= __('Sign in') ?></button>
    </form>

</div>
<script src="node_modules/fomantic-ui-css/semantic.min.js?<?= COMMIT_HASH ?>"></script>
<script src="assets/js/main.js?<?= COMMIT_HASH ?>"></script>
</body>
</html>
