<?php namespace App;

use Stichoza\GoogleTranslate\GoogleTranslate;

class Translation
{
    static $languageCodesInUse = [];
    static $languageCodeMinLength = 2;
    static $languageCodeMaxLength = 3;

    /**
     * @param bool $capitalize
     * @return array
     */
    static function languageCodesInUse(bool $capitalize): array
    {

        // Serve from cache, if possible
        if (self::$languageCodesInUse) {
            return $capitalize ? self::$languageCodesInUse : array_map('strtolower', self::$languageCodesInUse);
        }

        $languageColumns = get_all("SHOW COLUMNS FROM translations LIKE 'translationIn%'");
        foreach ($languageColumns as $column) {
            $code = substr($column['Field'], 13);
            self::$languageCodesInUse[] = $code;
        }

        // Return language codes in requested case
        return $capitalize ? self::$languageCodesInUse : array_map('strtolower', self::$languageCodesInUse);
    }

    static function get($criteria = null)
    {
        $where = SQL::getWhere($criteria);
        $translations = get_all("SELECT * FROM translations $where ORDER BY translationPhrase");
        ksort($translations);
        return $translations;
    }

    static function checkCodeForNewPhrases($dirs)
    {
        global $translations;

        $code_files = [];

        // Get a list of files to look within
        foreach ($dirs as $dir) {
            $code_files = array_merge($code_files, self::getFilesFromPath($dir));
        }

        // Scan files for phrases
        $current_phrases = self::findPhrases($code_files);

        // Set all phrases as not existing in the code
        q("UPDATE translations SET translationState = 'doesNotExist' WHERE translationState != 'dynamic'");

        if (!empty($current_phrases)) {

            $languages = Translation::languageCodesInUse(true);

            // Avoid issues with quotes in phrases by adding slashes before quotes
            foreach ($current_phrases as &$phrase) {

                if (strlen($phrase) > 765) {

                    // Shorten phrase
                    $phrase = substr($phrase, 0, 765);

                }

                // Make sure translations are loaded
                if (empty($translations)) {

                    get_translation_strings($_SESSION['language']);

                }

                // Escape the phrase
                $escapedPhrase = addslashes($phrase);

                // Insert only missing translations
                if (empty($translations) || !in_array($phrase, array_keys($translations))) {

                    // Prevent double records in the database
                    $translations[$phrase] = $phrase;

                    // Add the phrase to INSERT INTO translations
                    $valuesToInsert[] = "'$escapedPhrase'";

                    // Add the phrase to phrases to be translated
                    $phrasesToGoogleTranslate[] = $phrase;

                } else {
                    // Add the phrase to UPDATE translations
                    $valuesToUpdate[] = $escapedPhrase;
                }
            }

            if (!empty($valuesToInsert)) {

                // Convert $values array to string
                $valuesToInsert = '(' . implode('),(', $valuesToInsert) . ')';

                // Insert all strings to database
                q("INSERT INTO translations (translationPhrase)
                    VALUES $valuesToInsert");


            }
            if (!empty($valuesToUpdate)) {

                // Update existing translations
                foreach ($valuesToUpdate as $phrase) {
                    update('translations', [
                        'translationState' => 'existsInCode'
                    ], "translationPhrase = '$phrase'");
                }

            }

            // Delete strings that do not exist any more
            q("DELETE FROM translations WHERE translationState='doesNotExist'");

            // Google translate new strings
            foreach ($languages as $language) {
                self::googleTranslateMissingTranslations($language);
            }
        }
    }

    static function getFilesFromPath($path, $file_types = ['php', 'js']): array
    {
        $result = array();
        $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::FOLLOW_SYMLINKS);
        $filter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($file_types) {

            // Skip hidden files and directories.
            if ($current->getFilename()[0] === '.' || $current->getFilename()[0] === '..') {
                return FALSE;
            }
            if ($current->isDir()) {
                return true;

            } else {
                // Only consume files of interest.
                $pathinfo = pathinfo($current->getFilename());
                if (empty($pathinfo['extension'])) {
                    return false;
                }
                return in_array($pathinfo['extension'], $file_types);
            }
        });

        $files = new \RecursiveIteratorIterator($filter);

        foreach ($files as $file) {
            $result[] = $file->getPathname();
        }
        return $result;

    }

    /**
     * @param array $files Files to search
     * @return array Found phrases
     */
    private
    static function findPhrases(array $files): array
    {
        $phrases = [];

        foreach ($files as $file) {

            // Find __( 'xxx' ) and __( "xxx" );
            preg_match_all(
                "/__\(\"(?:[^\"\\\]|\\.)*\"\)|__\('(?:[^'\\\]|\\.)*'\)/",
                //"/__\(\"(?:[^\"\\\]|\\.)*\"\)|__\('(?:[^'\\\]|\\.)*'\)/",
                file_get_contents($file),
                $matches);

            $phrases = array_merge($phrases, $matches[0]);

        }

        $phrases = array_unique($phrases, SORT_STRING);

        // Remove _ _ ( ' from the beginning and ' ) from the end of the phrases
        array_walk($phrases, function (&$item) {
            $item = substr($item, 4, -2);
        });

        return $phrases;
    }

    public
    static function add(string $translationPhrase, $dynamic)
    {
        global $translations;

        insert('translations', [
            'translationPhrase' => $translationPhrase,
            'translationState' => $dynamic ? 'dynamic' : 'existsInCode'
        ]);

        // Prevent gaps in translations.translationId due to auto_increment increasing with ON DUPLICATE KEY UPDATE..
        $translations[$translationPhrase] = $translationPhrase;
    }

    public
    static function deleteLanguage($language)
    {
        if (empty($language) || strlen($language) != 2) {
            throw new \Exception('Invalid language');
        }
        q("ALTER TABLE translations DROP COLUMN translationIn$language");
    }

    public
    static function addLanguage($language)
    {
        if (empty($language) || strlen($language) != 2) {
            throw new \Exception('Invalid language');
        }

        // Re-format language
        $language = ucfirst(strtolower($language));

        q("ALTER TABLE translations ADD COLUMN translationIn$language varbinary(765)");
    }

    /**
     * Translates max 5000 characters
     * @param array $languages
     * @param array $googleTranslated
     */
    public
    static function googleTranslateMissingTranslations($language): void
    {

        // No language given
        if (empty($language))
            return;

        $payload = [];

        $untranslatedStrings = get_col("SELECT translationPhrase FROM translations WHERE translationIn$language IS NULL");

        // Build max 5000 char string
        foreach ($untranslatedStrings as $untranslatedString) {

            // Add to array
            $payload[] = $untranslatedString;

            // Check if the imploded length is over 5000
            if (strlen(implode("\n|\n", $payload)) >= 5000) {

                // Remove last member
                array_pop($payload);

                // Cancel foreach
                break;
            }
        }

        // Nothing to translate
        if (empty($payload)) {
            return;
        }

        // Translate payload
        $googleTranslated = explode("\n|\n",
            GoogleTranslate::trans(implode("\n|\n", $payload), $language, 'en'));

        // Loop over translated array
        for ($n = 0; $n < count($googleTranslated); $n++) {

            // Add translation to DB
            update(
                'translations', [
                'translationIn' . ucfirst($language) => substr($googleTranslated[$n], 0, 765)
            ], "translationPhrase = '" . addslashes($untranslatedStrings[$n]) . "'");
        }

    }

    public
    static function getUntranslated(array $languages)
    {
        // Capitalize first letter of each array member
        $languages = array_map('ucfirst', $languages);

        $criteria = empty($languages) ? [] :
            ['translationIn' . implode(' IS NULL OR translationIn', $languages) . ' IS NULL'];

        return self::get($criteria);
    }

    public
    static function getStatistics($languages = [])
    {
        $result = [];
        $sums = '';
        $languages = empty($languages) ? self::languageCodesInUse(false) : $languages;

        foreach ($languages as $language) {
            $sums .= "SUM(IF(translationIn$language IS NULL, 0, 1)) As translatedIn$language,";
            $sums .= "SUM(IF(translationIn$language IS NULL, 1, 0)) As remainingIn$language,";
        }

        $data = get_first("SELECT $sums COUNT(translationId) total FROM translations");

        foreach ($languages as $language) {
            $result['total'] = $data['total'];
            $result[$language]['remaining'] = $data['remainingIn' . $language];
            $result[$language]['translated'] = $data['translatedIn' . $language];
        }
        return $result;
    }

    private
    static function googleTranslate($textArray, $destinationLanguage)
    {
        $googleTranslated = GoogleTranslate::trans(implode("\n|\n", $textArray), strtolower($destinationLanguage), 'en');
        return explode("\n|\n", $googleTranslated);
    }

    static function getLanguages($criteria): array
    {

        $languages = [];
        $where = SQL::getWhere($criteria);
        $rows = get_all("SELECT translationLanguageName, translationLanguageCode FROM translationLanguages $where ORDER BY translationLanguageName");

        foreach ($rows as $item) {
            $languages[$item['translationLanguageCode']] = $item["translationLanguageName"];
        }

        return $languages;
    }

    static function getLanguagesByCode($languageCodes, $inverted = false)
    {
        $not = $inverted ? 'NOT' : '';
        return Translation::getLanguages([
            "translationLanguageCode $not IN('" . implode("','", $languageCodes) . "')"]);
    }
}