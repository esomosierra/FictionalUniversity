<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
if (file_exists(dirname(__FILE__) . '/local.php')) {
	// Local Database Settings.
	define( 'DB_NAME', 'local' );
	define( 'DB_USER', 'root' );
	define( 'DB_PASSWORD', 'root' );
	define( 'DB_HOST', 'localhost' );

} else {
	// Live Database Settings
	define( 'DB_NAME', 'edmons33_universitydata' );
	define( 'DB_USER', 'edmons33_wp774' );
	define( 'DB_PASSWORD', 'ibongmandaragit1914' );
	define( 'DB_HOST', 'localhost' );
}


/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '8FOr1wDWFsc9cLrG4G3M/mQabRXpJajqRKRQWQNSEfPW00tJMFHTb09ymqo3NEby4H+J6LeRhBPdBAk8EN2P2Q==');
define('SECURE_AUTH_KEY',  'UnHz+YiBDHL2A2tYvHfKhP3ByFZC0QSkHuPTERIne71+Bm+Zx4daoUxF5G0bwqZnUmLoI4RyhLTkecb/zEeNOA==');
define('LOGGED_IN_KEY',    'Ml/VbC4EWstpP1Q5w/q2C+ZZCcmpBf09B1W9vZjbs6bOtk2FOGyKbK8+WqzYAjNTeHHcy9LW/ZSHUuvaoAW2lA==');
define('NONCE_KEY',        'cjqPWm1N5U6iYiZTc4kl+/bC8D6Zs0+yehWE8wHbTQs5FItvV9BrT5W4Dpt0w9+PnOy3DSE94Aat3HbwGOiPCQ==');
define('AUTH_SALT',        'JMsf17c/nCqtUu08GIujGEEMKM5vAsJqnmS707NyE3X+8c7wFCf+WRbpnxhe9sfeueCPVhWOBj9dmrhkt7NA8Q==');
define('SECURE_AUTH_SALT', 'vN37HJrELiFNPaX5lbArLoZtAnfsfhhwEyC1+clPwkmfP1NobjnamnvY57YXeJxVr7QbaUb+b2EFKoPsubyUAg==');
define('LOGGED_IN_SALT',   'JeCrkWrgrYtl/ic11XcQLsSXixdQ/ORmt9qnPm/NlreSXZwZ/aRPr5wL9UBK2pNVohyuB/SV+MgdQNG2RV2mwQ==');
define('NONCE_SALT',       'MBdkAT19aXbOUU4mctfGt0xaF160XhGdxtKr4LxR9xlTd5fhdlbbrQT0NJ9DTOwF1YMlMxMgEm3Zl7h4X//uig==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

/* Inserted by Local by Flywheel. Fixes $is_nginx global for rewrites. */
if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) && strpos( $_SERVER['SERVER_SOFTWARE'], 'Flywheel/' ) !== false ) {
	$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.10.1';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
