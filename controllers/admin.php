<?php

class admin extends Controller
{

    function index(){}

    function POST_index()
    {

        // Check if the controller's table already exists in the database
        $table_names_are_singular = true;
        $name_plural = $_POST['name_plural'];
        $name_singular = $_POST['name_singular'];
        $table_name = $table_names_are_singular ? $name_singular : $name_plural;

        if (q("SHOW TABLES LIKE '$table_name'")) {

            // Show error
            echo '<div class="alert alert-danger">' . "The table $name_plural already existed. Aborting." . '</div>';

        } else {

            // Add table to database
            $name_plural = @mysql_real_escape_string($name_plural);
            q("CREATE TABLE `{$table_name}` (
             `{$name_singular}_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Autocreated',
             `{$name_singular}_name` varchar(50) NOT NULL COMMENT 'Autocreated',
             PRIMARY KEY (`{$name_singular}_id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            echo '<div class="alert alert-success">' . "The table $table_name was created ." . '</div>';

            // Add controller from template (substituting module for controller's name)
            $content = file_get_contents('config/controller_template.php');
            $content = str_replace('module', $name_singular, $content);
            $fp = fopen("controllers/$name_plural.php", "wb");
            fwrite($fp, $content);
            fclose($fp);

            /** Add views **/
            $views = ['index', 'view', 'edit'];

            // Create views directory
            $dirname = "views/$name_plural";
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755);
            }

            // Create each view
            foreach ($views as $view) {
                $content = file_get_contents("config/view_{$view}_template.php");
                $content = str_replace('module', $name_singular, $content);
                $fp = fopen("views/$name_plural/{$name_plural}_$view.php", "wb");
                fwrite($fp, $content);
                fclose($fp);
            }

            exec("git add controllers/*");
            exec("git add views/*");
            exec("chmod -R 777 *");


        }

    }

}