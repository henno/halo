<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= PROJECT_NAME ?></title>

    <!-- Fomantic UI CSS -->
    <link href="node_modules/fomantic-ui-css/semantic.min.css?<?= COMMIT_HASH ?>" rel="stylesheet">

    <!-- Site core CSS -->
    <link href="assets/css/main.css?<?=COMMIT_HASH?>" rel="stylesheet">

    <style>
        body {
            padding-top: 70px;
        }
    </style>
    <?php include 'templates/partials/favicon.php'?>

    <!-- jQuery -->
    <script src="node_modules/jquery/dist/jquery.min.js?<?=COMMIT_HASH?>"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js?<?=COMMIT_HASH?>"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js?<?=COMMIT_HASH?>"></script>
    <![endif]-->


<body>
<!-- Fomantic UI Menu -->
<div class="ui fixed inverted menu">
    <a class="header item" href="#">
        <?= PROJECT_NAME ?>
    </a>
    <div class="right menu">
        <a class="item <?= $action == 'users' ? 'active' : '' ?>" href="admin/users"><?= __('Users') ?></a>
        <a class="item <?= $action == 'logs' ? 'active' : '' ?>" href="admin/logs"><?= __('Logs') ?></a>
        <a class="item <?= $controller == 'halo' ? 'active' : '' ?>" href="halo"><?= __('Halo') ?></a>
        <a class="item <?= $action == 'translations' ? 'active' : '' ?>" href="admin/translations"><?= __('Translations') ?></a>
    </div>
    <?php require 'templates/partials/main_menu_right_side.php' ?>
</div>

<div class="ui container">
    <!-- Main content -->
    <?php if (!file_exists("views/$controller/{$controller}_$action.php")) error_out('The view <i>views/' . $controller . '/' . $controller . '_' . $action . '.php</i> does not exist. Create that file.'); ?>
    <?php @require "views/$controller/{$controller}_$action.php"; ?>
</div>

<?php require 'templates/partials/error_modal.php'; ?>

<!-- Fomantic UI core JavaScript -->
<script src="node_modules/fomantic-ui-css/semantic.min.js?<?= COMMIT_HASH ?>"></script>
<script src="assets/js/main.js?<?= COMMIT_HASH ?>"></script>
</body>

</html>
<?php require 'system/error_translations.php' ?>