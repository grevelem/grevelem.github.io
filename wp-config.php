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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'myspartansublease_wp');

/** MySQL database username */
define('DB_USER', 'myspartansubleas');

/** MySQL database password */
define('DB_PASSWORD', '!-nDfkP8');

/** MySQL hostname */
define('DB_HOST', 'mysql.myspartansublease.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Vj?n}hU~)rhguR 51$+8=O}HRp@g$f{68R i(/o%wP#)3/xb9otEa2;rSGV+OtGS');
define('SECURE_AUTH_KEY',  '.;zec066|^VqWK?c|LVj@R*NVSZi@Q[Ok;U-|S*QPpP8,c2))Bg#O,`tX(o$:1tV');
define('LOGGED_IN_KEY',    'Q#Qar^(oDj6>+N*BY]$N>X?+[:}Zd%@vUm-G.pV46y|DH(2w4sX8LTmUiSe,I7(G');
define('NONCE_KEY',        'VIU^P3-gk<jY|N{x1<d=z=8g@z~4p_q~0ZIoe|Zq ^(01~.WW^2!zf|id[4b{ 3K');
define('AUTH_SALT',        'h%AaTOfF>Dhf*xyAodiMvpfsc||pmuW/KC7)*f4!iDl/>v!43();4[AbX~8s@3S}');
define('SECURE_AUTH_SALT', 'jnWNSB<r/4sRf4ct5acOcnN|M[b$WJwHzF0-|,IIz*7Jm7?LyuD/a,]GGc3ZSse#');
define('LOGGED_IN_SALT',   'ZsvUox `tTJbrKU[bP?O%q/f?;HYx%BidL9x+{U!}b!7KwNna,jSmZ`R1h@?6fPf');
define('NONCE_SALT',       'nj1|)t]&jI&3W_3*`Z+!qpS&82agvh[xRCOVSYyMI0Qe VYt42Qn3|n/k|NYC;6U');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
