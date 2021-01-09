<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= PROJECT_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
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
<!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
</body>
</html>
