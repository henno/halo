<?php
if (isset($_POST['password'])) {
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    var_dump($p);

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form method="post">
    <input type="text" name="password">
    <button>Submit</button>
</form>
</body>
</html>