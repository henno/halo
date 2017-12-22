<?php

/**
 * Application's constants
 */

// Project constants
define('PROJECT_NAME', 'halo');
define('PROJECT_SESSION_ID', 'SESSID_HALO'); // For separating sessions of multiple Halo projects running on same server
define('DEFAULT_CONTROLLER', 'welcome');
define('DEVELOPER_EMAIL', 'dev@example.com'); // Where to send errors
define('FACEBOOK_APP_ID', '1000000000000001'); // For FB login
define('FACEBOOK_SECRET', 'ffffffffffffffffffffffffffffffff'); // For FB login
define('FORCE_HTTPS', false); // Force HTTPS connections
define('GOOGLE_CLIENT_ID', '1000000000000-ffffffffffffffffffffffffffffffff.apps.googleusercontent.com'); // For G login
define('GOOGLE_CLIENT_SECRET', 'sssssssssssssssss-ss_SSS');
define('GOOGLE_REDIRECT_URI', 'login_google/callback'); // For G login
define('WEBSITE_LANGUAGES', 'en|et'); // Languages this website supports. The first one is the default