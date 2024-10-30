<?php

/**
 * Activation of the plugin.
 * Fired during plugin activation
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Zendesk_Inbox_Activator
{
  /**
   * Activation of Inbox by Zendesk.
   *
   * @since 0.2
   */
  public static function activate() {
    update_option( 'zendesk_inbox_show', 1 );
  }

}
