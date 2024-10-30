<?php

/**
 * Includes utility functions used by other parts of the plugin
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Zendesk_Inbox_Utilities
{
  /**
   * Displays a view file. This will look for the correctly named view file in the `/views/` folder.
   *
   * @since 0.2
   *
   * @param String $name The filename of the view to display.
   * @param Array $params An associative array of variables you want to pass to the view.
   *        e.g. ['count' => 1], which you can do "<?php echo $count ?>;" in the view.
   */
  public static function view( $name, $params = array() ) {
    ob_start();

    extract( $params );
    include( INBOX_ZENDESK_PLUGIN_DIR . 'includes/views/' . $name . '.php' );

    ob_end_flush();
  }

  /**
   * Returns a css file's location based on the name. It looks in the `/assets/css/` folder.
   *
   * Use this with `wp_enqueue_style` to add css stylesheets to the pages.
   * an example on using this is below.
   *
   * wp_enqueue_style('zendesk-inbox-registration', Utilities::get_css_file_url('registration'));
   *
   * This will look for `registration.css` and add that to your page
   *
   * @since 0.2
   *
   * @param String $name
   * @return string
   */
  public static function get_css_file_url( $name ) {
    return INBOX_ZENDESK_PLUGIN_URL . 'assets/css/' . $name . '.css';
  }

  /**
   * Returns a javascript file's location based on the name. It looks in the `/assets/js/` folder.
   *
   * Use this with `wp_enqueue_scripts` to add javascript files to the pages.
   * an example on using this is below.
   *
   * wp_enqueue_scripts('zendesk-inbox-verification', Utilities::get_css_file_url('verification'));
   *
   * This will look for `verification.js` and add that to your page
   *
   * @since 0.2
   *
   * @param String $name
   * @return string
   */
  public static function get_js_file_url( $name ) {
    return INBOX_ZENDESK_PLUGIN_URL . 'assets/js/' . $name . '.js';
  }

}
