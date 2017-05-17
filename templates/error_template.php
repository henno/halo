<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?=BASE_URL?>">
    <title>Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" href="vendor/components/bootstrap/css/bootstrap.min.css">
    <style>
        .tooltip-inner {
            max-width: 200px;
            padding: 3px 8px;
            color: #fff;
            text-align: center;
            background-color: #000;
            border-radius: 4px;
        }
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .tooltip {
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            font-size: 12px;
            font-style: normal;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: left;
            text-align: start;
            text-shadow: none;
            text-transform: none;
            letter-spacing: normal;
            word-break: normal;
            word-spacing: normal;
            word-wrap: normal;
            white-space: normal;
        }
        .error {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 20px;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
        code {
            color: #444;
            border-radius: 3px;
            background-color: lightgray;
            padding: 4px;
        }
        .container {
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        * {
            box-sizing: border-box;
        }
        th {
            text-align: left;
        }
        table#stack {
            margin-top: -15px;
        }
        code {

        }
        pre code {
            background-color: #ddd !important;
            border-radius: 3px;
            padding: 4px;
            margin-bottom: 10px !important;
        }
    </style>
</head>

<body>

<div class="container">

    <br/>
    <br/>

    <div class="error"><strong>Error</strong><br><?= $error_msg ?></div>


</div>
<!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<script src="assets/js/clipboard.min.js"></script>
<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/components/bootstrap/js/bootstrap.min.js"></script>
<script>
    $('code').tooltip({
        trigger: 'click',
        placement: 'bottom'
    });

    function setTooltip(el, message) {
        $(el).tooltip('hide')
            .attr('data-original-title', message)
            .tooltip('show');
    }


    var clipboard = new Clipboard('code');

    clipboard.on('success', function(e) {
        setTooltip(e.trigger, 'Copied!');
    });

    clipboard.on('error', function(e) {
        setTooltip(e.trigger, 'Failed!');
    });
</script>
<!-- Placed at the end of the document so the pages load faster -->
</body>
</html>
