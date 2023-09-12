<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/ico/favicon.png">

    <title><?= PROJECT_NAME ?></title>

    <!-- Fomantic core CSS -->
    <link href="node_modules/fomantic-ui-css/semantic.min.css?<?= COMMIT_HASH ?>" rel="stylesheet">
    <!-- Site core CSS -->
    <link href="assets/css/main.css?<?= COMMIT_HASH ?>" rel="stylesheet">

    <!-- jQuery -->
    <script src="node_modules/jquery/dist/jquery.min.js?<?= COMMIT_HASH ?>"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js?<?= COMMIT_HASH ?>"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js?<?= COMMIT_HASH ?>"></script>
    <![endif]-->
</head>

<body>

<div class="ui inverted menu">
    <a class="header item" href="#">Navbar</a>
    <a class="item active" href="#">Home</a>
    <a class="item" href="#">Link</a>

    <div class="ui simple dropdown item">
        Dropdown
        <i class="dropdown icon"></i>
        <div class="menu">
            <a class="item" href="#">Action</a>
            <a class="item" href="#">Another Action</a>
            <div class="divider"></div>
            <a class="item" href="#">Something else here</a>
        </div>
    </div>

    <a class="item disabled">Disabled</a>

    <div class="right menu">
        <div class="item">
            <div class="ui transparent icon input">
                <input type="text" placeholder="Search..." aria-label="Search">
                <i class="search icon"></i>
            </div>
        </div>
        <div class="item">
            <button class="ui button">Search</button>
        </div>
    </div>
</div>

<div class="ui container">
    <?php
    /** @var string $controller set in Application::__construct() */
    /** @var string $action set in Application::__construct() */
    if (!file_exists("views/$controller/{$controller}_$action.php")) {
        error_out('The view <i>views/' . $controller . '/' . $controller . '_' . $action . '.php</i> does not exist. Create that file.');
    }
    @require "views/$controller/{$controller}_$action.php";
    ?>
</div>

<?php require 'templates/partials/error_modal.php'; ?>

<!-- Fomantic core JavaScript -->
<script src="node_modules/fomantic-ui-css/semantic.min.js?<?= COMMIT_HASH ?>"></script>
<script src="assets/js/main.js?<?= COMMIT_HASH ?>"></script>

</body>
</html>

<?php require 'system/error_translations.php' ?>
