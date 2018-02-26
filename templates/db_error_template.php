<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Database <?= empty($this->debug['error']) ? 'query debugger' : 'error' ?></title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style>

        .fault {
            background-color: yellow;
        }

        td, th {
            vertical-align: top;
            padding: 5px
        }
    </style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="container">

    <div class="alert alert-danger">

        <h1>Database <?= empty($this->debug['error']) ? 'query debugger' : 'error' ?></h1>


        <?php if (!empty($this->debug['debug_location'])): ?>
            <table>
                <tr>
                    <th>Debug flag set at</th>
                    <td><?= $this->debug['debug_location'] ?></td>
                </tr>
            </table>
        <?php endif ?>
        <table>
            <tr>
                <th>Query called at</th>
                <td><?= $this->debug['query_location'] ?></td>
            </tr>
        </table>

        <!-- ERROR
        ==========-->
        <?php if (!empty($this->debug['error'])): ?>
        <table>
            <tr>
                <th>Error message:</th>
                <td><?= $this->debug['error'] ?></td>
            </tr>
        </table>
        <?php endif ?>


        <!-- QUERY
        ==========-->
        <h3>Query</h3>
        <?php if (!empty($this->debug['sql'])): ?>
            <?php foreach ($this->debug['sql'] as $sql): ?>
                <?= $sql ?>
            <?php endforeach; ?>
        <?php elseif (!empty($this->debug['last_sql'])): ?>
                <?= $this->debug['last_sql'] ?>
        <?php endif ?>

        <!-- RESULT
        ==========-->
        <?php if (defined('DB_DEBUG') && isset($this->debug['result'])): ?>
            <h3><?= $this->debug['result_caption'] ?></h3>
            <div>
                <pre><?= pretty_print($this->debug['result']) ?></pre>
            </div>
        <?php endif ?>

    </div>
    <div class="alert alert-danger">
        <h3>Stack trace</h3>
        <?php require 'templates/partials/function_stack.php'; ?>
    </div>

</div><!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
