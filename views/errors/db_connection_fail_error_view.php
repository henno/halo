<strong>Error!</strong> <?php if ($error_type == 'connection_fail'): ?>
<?= mysql_error() ?>. Have you customized config.php yet? Are database host name,
its username and password correct
in config.php?
<br><br><a href="/phpmyadmin">localhost/phpmyadmin</a> | <a href="http://localhost/pma">localhost/pma</a> | <a
    href="https://github.com/phpmyadmin/phpmyadmin/archive/master.zip">Latest phpMyAdmin</a>
