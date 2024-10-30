<?php

  /**
   * Fired during plugin deactivation
   *
   * @link       https://signup.zendesk.com/inbox
   * @since      0.2
   *
   * @package    Inbox by Zendesk
   * @subpackage Inbox by Zendesk/includes
   */
/**
 * Deactivation of the plugin.
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Zendesk_Inbox_Deactivator
{
  /**
   * Deactivation of Inbox by Zendesk.
   *
   * @since 0.2
   */
  public static function deactivate () {
    update_option( 'zendesk_inbox_show', 0 );
  }
}
