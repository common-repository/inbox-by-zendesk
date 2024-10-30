<?php
/**
 * Fired when the plugin is uninstalled.
 */
// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

delete_option( 'zendesk_inbox_subdomain' );
delete_option( 'zendesk_inbox_show' );
delete_option( 'zendesk_inbox_widget_snippet' );
