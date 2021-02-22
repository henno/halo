<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= PROJECT_NAME ?></title>

    <!-- Bootstrap core CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css?<?=COMMIT_HASH?>" rel="stylesheet">

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


</head>

<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#"><?= PROJECT_NAME ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?=$action=='users'?'active':''?>">
                <a class="nav-link" href="admin/users"><?= __('Users') ?> <?=$action=='users'?'<span class="sr-only">(current)</span>':''?></a>
            </li>
            <li class="nav-item <?=$action=='logs'?'active':''?>">
                <a class="nav-link" href="admin/logs"><?= __('Logs') ?> <?=$action=='logs'?'<span class="sr-only">(current)</span>':''?></a>
            </li>
            <li class="nav-item <?=$controller=='halo'?'active':''?>">
                <a class="nav-link" href="halo"><?= __('Halo') ?> <?=$action=='halo'?'<span class="sr-only">(current)</span>':''?></a>
            </li>
            <li class="nav-item <?= $action == 'translations' ? 'active' : '' ?>">
                <a class="nav-link" href="admin/translations"><?= __('Translations') ?> <?= $action == 'translations' ? '<span class="sr-only">(current)</span>' : '' ?></a>
            </li>
        </ul>

        <?php require 'templates/partials/main_menu_right_side.php'?>
    </div>
</nav>

<div class="container">

    <!-- Main component for a primary marketing message or call to action -->
    <?php if (!file_exists("views/$controller/{$controller}_$action.php")) error_out('The view <i>views/' . $controller . '/' . $controller . '_' . $action . '.php</i> does not exist. Create that file.'); ?>
    <?php @require "views/$controller/{$controller}_$action.php"; ?>

</div>
<!-- /container -->

<?php require 'templates/partials/error_modal.php'; ?>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js?<?=COMMIT_HASH?>"></script>
<script src="assets/js/main.js?<?=COMMIT_HASH?>"></script>
</body>
</html>
<?php require 'system/error_translations.php' ?>