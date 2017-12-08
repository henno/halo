<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="vendor/components/bootstrap/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 70px;
        }
    </style>
</head>
<body>


<!-- Trigger -->
<div class="container">
    <div class="row">

        <code id="foo" title="terd" data-clipboard-target="#foo">asd</code>
    </div>

</div>


<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/components/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/clipboard.min.js"></script>
<script>
    // Tooltip

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
</body>
</html>