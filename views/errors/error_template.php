<!DOCTYPE html>
<html lang="en">
<head>
	<title><?= PROJECT_NAME ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?= ASSETS_URL ?>css/application.css" rel="stylesheet">
</head>

<body>

<div class="container">
<br />
<br />
	<div class="alert alert-danger">
		<? if (isset($errors)): ?>
			<h3>Tekkisid järgnevad vead: </h3>
			<li>
				<? foreach ($errors as $error): ?>
					<ul><?= $error ?></ul>
				<? endforeach; ?>
			</li>
		<? elseif (isset($error_file_name)): ?>
			<? require 'views/errors/'.$error_file_name.'_error_view.php' ?>
		<?
		else: ?>
			Tundmatu viga!

		<? endif; ?>
	</div>

</div>
<!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
</body>
</html>
