<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= BASE_URL ?>">
    <title><?= PROJECT_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <!-- Fomantic UI CSS -->
    <link href="node_modules/fomantic-ui-css/semantic.min.css?<?= COMMIT_HASH ?>" rel="stylesheet">

    <!-- Site core CSS -->
    <link href="assets/css/main.css?<?=COMMIT_HASH?>" rel="stylesheet">
</head>

<body>

<div class="container">

    <br/>
    <br/>

    <?php if (isset($errors)): ?>


        <?php foreach ($errors as $error): ?>

            <div class="alert alert-danger"><?= $error ?></div>

        <?php endforeach; ?>


    <?php else: ?>


        Unknown error!


    <?php endif; ?>

</div>

</body>
</html>
