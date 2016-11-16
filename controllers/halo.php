<?php namespace Halo;

class halo extends Controller
{

    function index()
    {
        try {
            $this->controllers_folder_is_writable = is_writable('controllers') ? true : false;
            $this->views_folder_is_writable = is_writable('views') ? true : false;
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    function POST_index()
    {

        // Check if the controller's table already exists in the database
        // $table_names_are_singular = true; # currently plural names are not supported
        $name_plural = $_POST['name_plural'];
        $name_singular = $_POST['name_singular'];
        $table_name = $name_plural;
        $table_prefix = $name_singular;

        if (q("SHOW TABLES LIKE '$table_name'")) {

            // Show error
            echo '<div class="alert alert-danger">' . "The table $name_plural already existed. Aborting." . '</div>';

        } else {

            // SQL injection protection
            global $db;
            $table_name_escaped = mysqli_real_escape_string($db, $table_name);
            $table_prefix_escaped = mysqli_real_escape_string($db, $table_prefix);

            // Add table to database
            q("CREATE TABLE `{$table_name_escaped}` (
             `{$table_prefix_escaped}_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Autocreated',
             `{$table_prefix_escaped}_name` varchar(50) NOT NULL COMMENT 'Autocreated',
             PRIMARY KEY (`{$table_prefix_escaped}_id`)
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

            // Print banner

            // Add 2 rows to database
            insert($table_name, array($table_prefix . '_name' => $name_singular . " #1"));
            insert($table_name, array($table_prefix . '_name' => $name_singular . " #2"));

            // Add controller from template (substituting module for controller's name)
            $content = file_get_contents('system/scaffolding/controller_template.php');
            $content = $this->replace_preserving_case("modules", $name_plural, $content);
            $content = $this->replace_preserving_case("module", $name_singular, $content);
            $controller_file = "controllers/$name_plural.php";
            $fp = fopen($controller_file, "wb");
            fwrite($fp, $content);
            fclose($fp);

            chmod($controller_file, 0666);

            /** Add views **/
            $views = ['index', 'view', 'edit'];

            // Create views directory
            $dirname = "views/$name_plural";
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755);
            }

            // Create each view
            foreach ($views as $view) {
                $content = file_get_contents("system/scaffolding/view_{$view}_template.php");
                $content = $this->replace_preserving_case("modules", $name_plural, $content);
                $content = $this->replace_preserving_case("module", $name_singular, $content);
                $view_file = "views/$name_plural/{$name_plural}_$view.php";
                $fp = fopen($view_file, "wb");
                fwrite($fp, $content);
                fclose($fp);
                chmod($view_file, 0666);

            }


            // Add files to git
            exec("git add controllers/*");
            exec("git add views/*");


            // Prevent git running under developer's user account having permission issues when commiting this file
            exec("chmod -R a+rwX *");


            echo '<div class="alert alert-success">' . 'The module <a href="' . BASE_URL . $table_name . '">' . $table_name . '</a> was created.</div>';
        }

    }

    private function replace_preserving_case($needle, $replacement, $haystack)
    {
        if (preg_match_all("/$needle/i", $haystack, $matches) !== FALSE) {
            foreach($matches[0] as $match){

                // Lowercase
                if ($match == strtolower($match)) {
                    $haystack = preg_replace("/$match/", strtolower($replacement), $haystack);
                }

                // Capitalized
                if ($match == ucfirst($match)) {
                    $haystack = preg_replace("/$match/", ucfirst($replacement), $haystack);
                }

                // All caps
                if ($match == strtoupper($match)) {
                    $haystack = preg_replace("/$match/", strtoupper($replacement), $haystack);
                }

            }
        }

        return $haystack;
    }

    function generate_password_hash(){
        exit( password_hash($_POST['password'], PASSWORD_DEFAULT) );
    }

}