<?php
/**
 * Unit Tests bootstrap file.
 *
 * @package OptionTree
 * @since 2.7.0
 */

/**
 * Determine if we should update the content and plugin paths.
 */
if ( ! defined( 'WP_CONTENT_DIR' ) && getenv( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', getenv( 'WP_CONTENT_DIR' ) );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	if ( file_exists( dirname( __DIR__ ) . '/wp-load.php' ) ) {
		define( 'WP_CONTENT_DIR', dirname( __DIR__ ) . '/wp-content' );
	} elseif ( file_exists( '../../../wp-content' ) ) {
		define( 'WP_CONTENT_DIR', dirname( dirname( dirname( getcwd() ) ) ) . '/wp-content' );
	}
}

if ( defined( 'WP_CONTENT_DIR' ) && ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', rtrim( WP_CONTENT_DIR, '/' ) . '/plugins' );
}

if ( file_exists( __DIR__ . '/../phpunit-plugin-bootstrap.project.php' ) ) {
	require_once __DIR__ . '/../phpunit-plugin-bootstrap.project.php';
}

global $_plugin_file;

$_tests_dir = getenv( 'WP_TESTS_DIR' );

// Travis CI & Vagrant SSH tests directory.
if ( empty( $_tests_dir ) ) {
	$_tests_dir = '/tmp/wordpress-tests';
}

// Relative path to Core tests directory.
if ( ! is_dir( $_tests_dir . '/includes/' ) ) {
	$_tests_dir = '../../../../tests/phpunit';
}

if ( ! is_dir( $_tests_dir . '/includes/' ) ) {
	// phpcs:disable
	trigger_error( 'Unable to locate wordpress-tests-lib', E_USER_ERROR );
	// phpcs:enable
}
require_once $_tests_dir . '/includes/functions.php';

$_plugin_dir = getcwd();
foreach ( glob( $_plugin_dir . '/*.php' ) as $_plugin_file_candidate ) {
	// @codingStandardsIgnoreStart
	$_plugin_file_src = file_get_contents( $_plugin_file_candidate );
	// @codingStandardsIgnoreEnd
	if ( preg_match( '/Plugin\s*Name\s*:/', $_plugin_file_src ) ) {
		$_plugin_file = $_plugin_file_candidate;
		break;
	}
}
if ( ! isset( $_plugin_file ) ) {
	// phpcs:disable
	trigger_error( 'Unable to locate a file containing a plugin metadata block.', E_USER_ERROR );
	// phpcs:enable
}
unset( $_plugin_dir, $_plugin_file_candidate, $_plugin_file_src );

/**
 * Force plugins defined in a constant (supplied by phpunit.xml.dist) to be active at runtime.
 *
 * @filter site_option_active_sitewide_plugins
 * @filter option_active_plugins
 *
 * @param array $active_plugins All active plugins.
 * @return array
 */
function _phpunit_filter_active_plugins( $active_plugins ) {
	$forced_active_plugins = array();
	if ( defined( 'WP_TEST_ACTIVATED_PLUGINS' ) ) {
		$forced_active_plugins = preg_split( '/\s*,\s*/', WP_TEST_ACTIVATED_PLUGINS );
	}
	if ( ! empty( $forced_active_plugins ) ) {
		foreach ( $forced_active_plugins as $forced_active_plugin ) {
			$active_plugins[] = $forced_active_plugin;
		}
	}
	return $active_plugins;
}
tests_add_filter( 'site_option_active_sitewide_plugins', '_phpunit_filter_active_plugins' );
tests_add_filter( 'option_active_plugins', '_phpunit_filter_active_plugins' );

/**
 * Load the plugin.
 */
function _phpunit_load_plugin_file() {
	global $_plugin_file;

	// Load this plugin.
	require_once $_plugin_file;
	unset( $_plugin_file );
}
tests_add_filter( 'muplugins_loaded', '_phpunit_load_plugin_file' );

require $_tests_dir . '/includes/bootstrap.php';
