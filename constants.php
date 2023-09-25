<?php

/**
 * Application's constants
 */

// Project constants
const PROJECT_NAME = 'halo';
const PROJECT_SESSION_ID = 'SESSID_HALO'; // For separating sessions of multiple Halo projects running on same server
const DEFAULT_CONTROLLER = 'welcome';
const DEVELOPER_EMAIL = 'dev@example.com'; // Where to send errors
const FACEBOOK_APP_ID = '1000000000000001'; // For FB login
const FACEBOOK_SECRET = 'ffffffffffffffffffffffffffffffff'; // For FB login
const FORCE_HTTPS = false; // Force HTTPS connections
const GOOGLE_CLIENT_ID = '1000000000000-ffffffffffffffffffffffffffffffff.apps.googleusercontent.com'; // For G login
const GOOGLE_CLIENT_SECRET = 'sssssssssssssssss-ss_SSS';
const GOOGLE_REDIRECT_URI = 'login_google/callback'; // For G login
const DEFAULT_TIMEZONE = 'Europe/Tallinn';
const ENV_DEVELOPMENT = 0;
const ENV_PRODUCTION = 1;
const ACTIVITY_LOGIN = 1;
const ACTIVITY_LOGOUT = 2;