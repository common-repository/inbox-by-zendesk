<?php
/**
 * Plugin Name: Inbox by Zendesk
 * Plugin URI: https://www.zendesk.com/inbox
 * Description: The new Inbox by Zendesk plugin brings modern team email right into Wordpress.  Simply place a contact form on your site, or publish your hello@yourcompany email address to get started. All messages are managed in Inbox’s easy-to-use shared email interface, where your team conversations happen faster, and your client’s questions get answered correctly.  Sign up for free, add a few teammates, and try it out today.
 * Version: 1.0.3
 * Author: Zendesk
 * Author URI: https://www.zendesk.com/
 * License: Apache License Version 2.0
 */

// Define the plugins constants
define( 'INBOX_ZENDESK_VERSION', '1.0.3' );
define( 'INBOX_ZENDESK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'INBOX_ZENDESK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'INBOX_ZENDESK_SMALL_LOGO', INBOX_ZENDESK_PLUGIN_URL . 'assets/img/icon-16.png' );

if ( ! defined( 'INBOX_ZENDESK_DOMAIN' ) ) {
  define( 'INBOX_ZENDESK_DOMAIN', 'zendesk.com' );
}

// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Activation of the plugin.
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Inbox_Zendesk
{
  private static $instance = null;

  /**
   * Initializes the plugin
   */
  private function __construct() {
    spl_autoload_register( array( &$this, 'autoload' ) );

    // Load plugin text domain
    load_plugin_textdomain( 'zendesk-inbox', false, basename( dirname( __FILE__ ) ) . '/languages' );

    // Register activation hook
    register_activation_hook( __FILE__, array( &$this, 'activate_inbox' ) );
    register_deactivation_hook( __FILE__, array( &$this, 'deactivate_inbox' ) );

    if ( is_admin() ) {
      $admin = Zendesk_Inbox_Admin::get_instance();
      $admin->set_api_client( new Zendesk_Inbox_API() );
    } else {
      // Add user facing features
      add_action( 'wp_footer', array( Zendesk_Inbox_Blog::get_instance(), 'insert_widget' ) );
    }
  }

  /*
   * autoloading callback function
   * @param string $class name of class to autoload
   * @return TRUE to continue; otherwise FALSE
   */
  public function autoload( $class ) {
    // setup the class name
    $classname = str_replace( 'Zendesk_Inbox_', '', $class );
    $classname = strtolower( str_replace( '_', '-', $classname ) );

    $classfile = INBOX_ZENDESK_PLUGIN_DIR . 'includes/class-' . $classname . '.php';

    if ( file_exists( $classfile ) ) {
      require_once( $classfile );
    }
  }

  /**
   * Returns a singleton instance for Inbox_Zendesk
   * @return object Instance of Inbox_Zendesk
   */
  public static function get_instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * The code that runs during plugin activation.
   * This action is documented in includes/class-inbox-zendesk-activator.php
   */
  public static function activate_inbox() {
    Zendesk_Inbox_Activator::activate();
  }

  /**
   * The code that runs during plugin activation.
   * This action is documented in includes/class-inbox-zendesk-deactivator.php
   */
  public static function deactivate_inbox() {
    Zendesk_Inbox_Deactivator::deactivate();
  }
}

/**
 * Instantiate the class for the plugin here.
 *
 * @since 0.2
 */
Inbox_Zendesk::get_instance();
