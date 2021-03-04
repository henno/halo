<?php namespace App;

use DateTime;
use HTMLPurifier;
use HTMLPurifier_Config;

class admin extends Controller
{
    public $requires_auth = true;
    public $requires_admin = true;
    public $template = 'admin';

    public function translations()
    {
        $languageCodesInUse = Translation::languageCodesInUse(true);
        $showUntranslated = empty($_GET['showUntranslated']) ? [] : explode(',', $_GET['showUntranslated']);
        $this->languagesInUse = Translation::getLanguagesByCode($languageCodesInUse);
        $this->languagesNotInUse = Translation::getLanguagesByCode($languageCodesInUse, true);
        $this->showUntranslated = array_flip($showUntranslated);
        $this->translations = Translation::getUntranslated($showUntranslated);
        $this->statistics = Translation::getStatistics();
        $this->translations_where_phrase_is_too_long = Translation::get(['LENGTH(translationPhrase) >= 765']);

    }

    public function AJAX_translationEdit()
    {

        if (empty($_POST['translationId']) || !is_numeric($_POST['translationId']) || $_POST['translationId'] <= 0) {
            stop(400, 'Invalid translationId');
        }
        if (!Translation::isValidLanguageCode($_POST['languageCode'])) {
            stop(400, "Invalid languageCode");
        }
        if ($this->htmlIsValid($_POST['translation'])) {
            stop(400, "Invalid HTML");
        }

        update('translations', [
            "translationIn$_POST[languageCode]" => $_POST['translation']
        ], "translationId = $_POST[translationId]");
    }

    public function AJAX_translationAddLanguage()
    {

        if (!Translation::isValidLanguageCode($_POST['languageCode'])) {
            stop(400, "Invalid languageCode");
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        Translation::addLanguage($_POST['languageCode']);

        Translation::googleTranslateMissingTranslations($_POST['languageCode']);
    }

    public function AJAX_translationDeleteLanguage()
    {

        if (!Translation::isValidLanguageCode($_POST['languageCode'])) {
            stop(400, "Invalid languageCode");
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        Translation::deleteLanguage($_POST['languageCode']);

    }

    public function AJAX_translateRemainingStrings()
    {
        if (!Translation::isValidLanguageCode($_POST['languageCode'])) {
            stop(400, "Invalid languageCode");
        }
        Translation::googleTranslateMissingTranslations($_POST['languageCode']);
        $stats = Translation::getStatistics([$_POST['languageCode']]);

        // Return remaining untranslated string count
        stop(200, ['untranslatedCount' => $stats[$_POST['languageCode']]['remaining']]);

    }

    function index()
    {
        header('Location: ' . BASE_URL . 'admin/users');
        exit();
    }

    function users()
    {
        $this->users = User::get(null, 'userId DESC');
    }

    function logs()
    {
        $this->log = Activity::logs();
    }

    function AJAX_addUser()
    {
        if (empty($_POST['userName'])) {
            stop(400, __('Invalid username'));
        }
        if (empty($_POST['userPassword'])) {
            stop(400, __('Invalid password'));
        }

        $userName = addslashes($_POST['userName']);
        if (User::get(["userName = '$userName'"])) {
            stop(409, __('User already exists'));
        }

        User::register($_POST['userName'], $_POST['userEmail'], $_POST['userPassword']);

        stop(200);
    }

    function AJAX_editUser()
    {
        if (empty($_POST['userId']) || !is_numeric($_POST['userId'])) {
            stop(400, 'Invalid' . ' userId');
        }

        // Remove empty password from $_POST or hash it
        if (empty($_POST['userPassword'])) {
            unset($_POST['userPassword']);
        } else {
            $_POST['userPassword'] = password_hash($_POST['userPassword'], PASSWORD_DEFAULT);
        }

        User::edit($_POST['userId'], $_POST);


        stop(200);
    }

    function AJAX_deleteUser()
    {
        if (empty($_POST['userId']) || !is_numeric($_POST['userId'])) {
            stop(400, 'Invalid' . ' userId');
        }

        if ($_POST['userId'] == $this->auth->userId) {
            stop(403, __('You cannot delete yourself'));
        }

        User::delete($_POST['userId']);

        stop(200);
    }

    function AJAX_getUser()
    {
        if (empty($_POST['userId']) || !is_numeric($_POST['userId'])) {
            stop(400, 'Invalid' . ' userId');
        }

        stop(200, get_first("SELECT userIsAdmin,userEmail,userName FROM users WHERE userId = $_POST[userId]"));
    }

    public function htmlIsValid($html): bool
    {

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $purifier = new HTMLPurifier($config);
        $pure_html = $purifier->purify($html);

        return !!strcmp($html, $pure_html);

    }

}