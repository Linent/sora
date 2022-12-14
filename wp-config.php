<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'bd_sora' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '{1t9E#AShDth7uu3eH1CDnQ 4y=|_y.,[6X{eL;rSU55U!|RsH}5PGSqEjuAXu(0' );
define( 'SECURE_AUTH_KEY',  '%&RPEhG)Nb.MW-DHG1s]_/G,7cxmTQwP8Y#}6L{7}~HKZa]-{B+@zu)W:Z)HTf3n' );
define( 'LOGGED_IN_KEY',    '7/klaRPWS:^nxX(rI0Z2#sdNkX3tw25IDM/NCHG3:~Xk=F>QDnr%QT_aB|<cb1xl' );
define( 'NONCE_KEY',        '%vk^0r|T6#^0_mCyT`$5<.k^!D83w(N]8q^<oyzI@?,I5 OAO`Ei-:[o?wQ79?L=' );
define( 'AUTH_SALT',        '!8:ojjVtq-$s.PFzmT|Y(TpW2OteUKD}jzlr+)o^RTgUAbP.hd0*hC)JJBm<rNIw' );
define( 'SECURE_AUTH_SALT', 'pL+a5[`B+OR+F5r-hy.~-nM=ZO?u[`^1@ePQR,DJS~QPYwR6RO7U~EQ}itTW<-X8' );
define( 'LOGGED_IN_SALT',   'K(k/vQc=-8f,{qpZE5)J<Vnh>?<KWImCq,1^9x&2{njDN(bgl&D4Vx9 Br %,c[q' );
define( 'NONCE_SALT',       'rgf;z`X^s#|<4p8`u2FMS);h,bVrh|_<gd2i*O?Je$4:mOU X{R&vhb8oDnw)So8' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
