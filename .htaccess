RewriteEngine On

RewriteRule ^(classes) - [F,L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule .* index.php/$0 [PT,L]

# Enable <? ?>
php_value short_open_tag 1

# enable PHP error logging
php_flag  log_errors on
php_value error_log  system/logs/PHP_errors.log

# prevent access to PHP error log
<Files PHP_errors.log>
 Order allow,deny
 Deny from all
 Satisfy All
</Files>