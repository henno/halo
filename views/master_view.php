<!DOCTYPE html>
<html class="no-js lt-ie9 lt-ie8 lt-ie7">
<html class="no-js lt-ie9 lt-ie8">
<html class="no-js lt-ie9">
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Pealkiri</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css" rel="stylesheet">
	<script src="<?=ASSETS_URL ?>js/vendor/modernizr-2.6.2.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?=ASSETS_URL?>js/vendor/jquery-1.9.1.min.js"><\/script>')</script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
	<script src="<?=ASSETS_URL?>js/plugins.js"></script>
	<script src="<?=ASSETS_URL?>js/main.js"></script>
	<script>BASE_URL = '<?=BASE_URL?>'</script>

	<?if(!EMPTY($this->scripts)) : ?>
		<?foreach($this->scripts as $script) : ?>
			<script src="<?=ASSETS_URL?>js/<?=$script?>"></script>
		<?endforeach?>
	<?endif?>

	<style>
		body {
			padding-top: 60px;
		}
		body, html {
			background: url('<?=BASE_URL?>assets/img/iostexture.png');
			height: 100%;
		}
		table.table-bordered tr {
			background-color: #f9f9f9;
		}
	</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="brand" href="#">Projekti nimi</a>
			<div class="nav-collapse collapse">
				<ul class="nav">
					<li class="active"><a href="<?=BASE_URL?>tests">Esimene leht</a></li>
					<li><a href="#about">Info</a></li>
					<li><a href="<?=BASE_URL?>auth/logout">Logi välja</a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>
<div>
	<?php
	require 'views/'.$request->controller.'_'.$request->action.'_view.php';
	?>
</div>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->


</body>
</html>
