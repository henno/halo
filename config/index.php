<?
if (isset($_POST['module'])) {

    // Bootstrap mini-framework
    define('PROJECT_NAME', 'Halo');
    require 'config.php';
    require '../system/database.php';

    // Check if the module's table already exists in the database
    $module = substr($_POST['module'], -1) == 's' ? substr($_POST['module'], 0, -1) : $_POST['module'];
    if (q("SHOW TABLES LIKE '$module'")) {

        // Show error
        echo '<div class="alert alert-danger">' . "The table $module already existed. Aborting." . '</div>';

    } else {

        // Add table to database
        $table = @mysql_real_escape_string($module);
        q("CREATE TABLE `{$module}` (
             `{$module}_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Autocreated',
             `{$module}_name` varchar(50) NOT NULL COMMENT 'Autocreated',
             PRIMARY KEY (`{$module}_id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;");
        echo '<div class="alert alert-success">' . "The table $module was created ." . '</div>';

        // Add controller
        $content = file_get_contents('controller_template.php');
        $content = str_replace('module', $module, $content);
        $fp = fopen("../controllers/$module.php", "wb");
        fwrite($fp, $content);
        fclose($fp);

        /** Add views **/
        $views = ['index', 'view', 'edit'];

        // Create views directory
        $dirname = "../views/$module";
        if (!is_dir($dirname))
        {
            mkdir($dirname, 0755);
        }

        // Create each view
        foreach($views as $view){
            $content = file_get_contents("view_{$view}_template.php");
            $content = str_replace('module', $module, $content);
            $fp = fopen("../views/$module/{$module}_$view.php", "wb");
            fwrite($fp, $content);
            fclose($fp);
        }

    }

}
?>
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

    <!-- Bootstrap core CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style>
        body {
            min-height: 2000px !important;
            padding-top: 70px;
        }
    </style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<div class="container">
    <h1>Halo configuration page</h1>

    <p class="help-block">
        This page allows you automatically create a new module which includes
    <ul>
        <li>a database table,</li>
        <li>a controller with 3 actions (index, view, edit)</li>
        <li>views (index, view, edit)</li>
    </ul>
    </p>
    <p class="help-block">Please input new module name in plural, e.g. <i>users</i> (when applicable):</p>
    <!-- Main component for a primary marketing message or call to action -->

    <form method="post">

        <div class="input-group">
            <span class="input-group-addon">Module name</span>
            <input type="text" class="form-control" placeholder="users" name="module">
        </div>
        <p>

        <div class="pull-right">
            <button class="btn btn-primary" type="submit">Add</button>
        </div>
        </p>
    </form>

</div>
<!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="../assets/components/jquery/1.10.2/jquery-1.10.2.min.js"></script>
<script src="../assets/components/bootstrap/3.0.3/js/bootstrap.min.js"></script>
</body>
</html>
