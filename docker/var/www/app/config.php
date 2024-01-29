<?php

defined('ENABLE_URL_REWRITE') or define('ENABLE_URL_REWRITE', true);
defined('LOG_DRIVER') or define('LOG_DRIVER', 'system');


// Database driver: sqlite, mysql, postgres, odbc, dblib, or mssql (sqlite by default)
define('DB_DRIVER', 'postgres');

// Database username
define('DB_USERNAME', 'ricaiachi');

// Database password
define('DB_PASSWORD', 'PIOb2kuscg6G');

// Database hostname
define('DB_HOSTNAME', 'ep-cold-fog-a58ourj0.us-east-2.aws.neon.tech');

// Database database name
define('DB_NAME', 'kanboard');

// Plugins directory
define('PLUGINS_DIR', __DIR__.DIRECTORY_SEPARATOR.'plugins');

// Plugins directory URL
define('PLUGIN_API_URL', 'https://kanboard.org/plugins.json');

// Enable/Disable plugin installer (Disabled by default for security reasons)
// There is no code review or any approval process to submit a plugin.
// This is up to the Kanboard instance owner to validate if a plugin is legit.
define('PLUGIN_INSTALLER', true);
defined('ENABLE_URL_REWRITE') or define('ENABLE_URL_REWRITE', true);
defined('LOG_DRIVER') or define('LOG_DRIVER', system);
