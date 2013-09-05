<? if(DATABASE_DATABASE == 'halo'): ?>
	You must customize the parameter DATABASE_DATABASE in config.php to your project's database name in MySQL server.
<? else :?>
	There is no database '<?= DATABASE_DATABASE ?>' in MySQL server. Check the value of DATABASE_DATABASE
	in config.php or create database '<?= DATABASE_DATABASE ?>' in MySQL server.
<? endif ?>
<br><br><a href="/phpmyadmin">localhost/phpmyadmin</a> | <a href="http://localhost/pma">localhost/pma</a> | <a
	href="https://github.com/phpmyadmin/phpmyadmin/archive/master.zip">Latest phpMyAdmin</a>
