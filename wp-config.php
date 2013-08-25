<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/cms');
define('WP_HOME',    'http://' . $_SERVER['SERVER_NAME']);
//define( 'WP_DEFAULT_THEME', 'apw' );
define( 'WP_CONTENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/content' );
define( 'WP_PLUGIN_DIR', $_SERVER['DOCUMENT_ROOT'] . '/plugins' );
define( 'WP_PLUGIN_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/plugins' );
define( 'PLUGINDIR', 'http://' . $_SERVER['SERVER_NAME'] . '/plugins' );

// Sets the config file based on current environment
if ( strpos( $_SERVER['HTTP_HOST'], 'dev' ) !== false ) {
	$config_file = 'config/db-settings.dev.php';
} else {
	$config_file = 'config/db-settings.prod.php';
}

// include the config file if it exists, otherwise WP is going to fail
$path = dirname(__FILE__) . '/';
if ( file_exists( $path . $config_file ) ) {
	require_once $path . $config_file;
}

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true if you are planning on modifying some of WordPress' built-in JavaScript or Cascading Style Sheets.
 */
define('SCRIPT_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
