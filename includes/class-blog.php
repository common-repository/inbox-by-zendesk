<?php

/**
 * Handles the non-admin user facing part of the site
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Zendesk_Inbox_Blog
{
  protected static $instance = null;

  public static function get_instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  /**
   * Inserts the widget onto the main page if the option to show is set to 1.
   * This will create a `widget.js` file in the JS folder because that is safer to load.
   */
  public function insert_widget() {
    if ( ! get_option( 'zendesk_inbox_show', $default = 1 ) || ! get_option( 'zendesk_inbox_subdomain', $default = '' ) ) {
      return;
    }

    $api = new Zendesk_Inbox_API();
    $widget_code = $api->get_widget_code();

    if ( ! is_wp_error( $widget_code ) ) {
      echo '<script type="text/javascript">' . $widget_code . '</script>';
    }
  }
}
