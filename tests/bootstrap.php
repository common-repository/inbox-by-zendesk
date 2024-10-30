<?php

define('INBOX_ZENDESK_DOMAIN', 'zendesk.com');

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	exec( 'bash bin/install-wp-tests.sh' );
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../inbox_zendesk.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

define('INBOX_ZENDESK_SUBDOMAIN', 'z3nmio1');
