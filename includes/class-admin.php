<?php

/**
 * This will handle the  main admin functions of the plugin like registration and verification
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Zendesk_Inbox_Admin
{
  protected static $instance = null;
  private $api = null;

  /**
   * Private constructor for singleton implementation
   */
  private function __construct() {
    // Add admin menu
    add_action( 'admin_menu', array( &$this, 'admin_add_menu' ) );
    add_action( 'admin_init', array( &$this, 'admin_init' ) );
    add_action( 'admin_enqueue_scripts', array( &$this, 'load_css_and_js' ) );
    add_action( 'update_option_zendesk_inbox_subdomain', array( &$this, 'after_subdomain_update' ) );
  }

  public static function get_instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function set_api_client( $api ) {
    $this->api = $api;
  }

  /**
   * Determines what page to show and routes to the correct page
   */
  public function show_admin_page()
  {
    if ( $subdomain = get_option( 'zendesk_inbox_subdomain', $default = false ) ) {
      $this->display_inbox_iframe( $subdomain );
    } elseif ($post_data = $_POST) {
      $this->create_account( $post_data );
    } else {
      $this->display_register_page();
    }
  }

  /**
   * Receives the post hook and calls the api to create a new zendesk/inbox account. It displays the verification link after
   * registration.
   *
   * @param Array $post_data
   */
  public function create_account( $post_data ) {
    $current_user = wp_get_current_user();

    $response = $this->api->create_account(
      $post_data[ 'zendesk_api_account' ][ 'name' ],
      $post_data[ 'zendesk_api_account' ][ 'subdomain' ],
      $current_user->data->user_nicename,
      $current_user->data->user_email
    );

    if ( is_wp_error( $response ) ) {
      $error_data = array(
        'error_code'        => $response->get_error_code(),
        'error_description' => '',
        'error_message'     => $response->get_error_message(),
      );

      $this->display_register_page( $error_data );
    } else {
      $response_body = json_decode( $response[ 'body' ], true );
      // Add other success codes here if more arise
      if ( in_array( $response[ 'response' ][ 'code' ], array( 200, 201 ) ) ) {
        update_option( 'zendesk_inbox_subdomain', $post_data[ 'zendesk_api_account' ][ 'subdomain' ] );

        $this->display_verification_page( array( 'verification_link' => $response_body[ 'owner_verification_link' ] ) );
        // Force delete the snippet option, to refresh the value when Api::get_widget_code() is called
        delete_option( 'zendesk_inbox_widget_snippet' );
      } else {
        $error_data = array(
          'error_code'        => $response[ 'response' ][ 'code' ],
          'error_description' => $response_body[ 'description' ],
          'error_message'     => $response_body[ 'description' ],
        );
        // If we have a more detailed description use that for the record.
        if ( 'RecordInvalid' == $response_body['error'] ) {
          if ( array_key_exists( 'details', $response_body ) && ( $key = key( $response_body[ 'details' ] ) ) ) {
            $error_data[ 'error_message' ] = $response_body[ 'details' ][ $key ][0][ 'description' ];
          }
        }
        $this->display_register_page( $error_data );
      }
    }

  }

  protected function display_verification_page( $params = array() ) {
    Zendesk_Inbox_Utilities::view('admin/verification', $params ); 
  }

  /**
   * Displays the form to register for a zendesk/inbox account.
   *
   * @param Array $params
   */
  protected function display_register_page( $params = array() ) {
    Zendesk_Inbox_Utilities::view( 'admin/registration', $params );
  }

  /**
   * Displays the iframe containing inbox
   *
   * @param string $subdomain
   */
  private function display_inbox_iframe( $subdomain ) {
    $params = array( 'src' => sprintf( 'https://%s.%s', $subdomain, trailingslashit( INBOX_ZENDESK_DOMAIN ) ) );
    wp_enqueue_style( 'zendesk-inbox-admin' );

    Zendesk_Inbox_Utilities::view( 'admin/main', $params );
  }


  /**
   * The code that shows the registration page.
   * This action is documented in includes/class-inbox-zendesk-register.php
   */
  public function admin_add_menu() {
    add_menu_page( 'Inbox', __( 'Inbox by Zendesk', 'zendesk-inbox' ), 'edit_posts', 'inbox-by-zendesk',
      array( &$this, 'show_admin_page' ), INBOX_ZENDESK_SMALL_LOGO );
    add_submenu_page( 'inbox-by-zendesk', 'Inbox by Zendesk Settings', __( 'Settings', 'zendesk-inbox' ), 'manage_options', 'inbox-by-zendesk-settings',
      array( &$this, 'display_settings_page' ) );
  }

  /**
   * Initialize the admin section of the plugin
   */
  public function admin_init() {
    register_setting( 'zendesk_inbox_settings', 'zendesk_inbox_show', array( &$this, 'validate_show_setting' ) );
    register_setting( 'zendesk_inbox_settings', 'zendesk_inbox_subdomain' );

    add_settings_section( 'zendesk_inbox_main', __( 'Settings', 'zendesk-inbox' ),
      array( &$this, 'settings_section_content' ), 'inbox-by-zendesk' );

    add_settings_field( 'zendesk_inbox_show', __( 'Show widget on my site', 'zendesk-inbox' ),
      array( &$this, 'settings_show_field_content' ), 'inbox-by-zendesk', 'zendesk_inbox_main', array( 'label_for' => 'zendesk_inbox_show' ) );
    add_settings_field('zendesk_inbox_subdomain', __('Subdomain', 'zendesk-inbox'),
      array( &$this, 'settings_show_subdomain_field_content' ), 'inbox-by-zendesk', 'zendesk_inbox_main', array( 'label_for' => 'zendesk_inbox_subdomain' ) );
  }

  /**
   * Displays the settings page
   *
   * @param Array $params
   */
  public function display_settings_page() {
    Zendesk_Inbox_Utilities::view( 'admin/settings' );
  }

  /**
   * Returns the settings section content
   */
  public function settings_section_content() {
    echo '<p>' . __( 'Main Settings', 'zendesk-inbox' ) . '</p>';
  }

  /**
   * Returns the show field content
   */
  public function settings_show_field_content() {
    $current_value = get_option( 'zendesk_inbox_show', 1 );

    echo '<input type="checkbox" id="zendesk_inbox_show" name="zendesk_inbox_show[value] value="1" ' . checked( $check_if_val = 1, $current_value, $echo = false ) . '>';
  }

  /**
   * Shows the subdomain input box
   */
  public function settings_show_subdomain_field_content() {
    echo '<input autocomplete="off" class="form-control last-input" id="zendesk_inbox_subdomain"
            value="', esc_attr( get_option( 'zendesk_inbox_subdomain', '' ) ), '"
            name="zendesk_inbox_subdomain" placeholder="', __( 'subdomain', 'zendesk-inbox' ), '" required="required" 
            type="text" />
            <span class="input-group-addon">.', INBOX_ZENDESK_DOMAIN, '</span>',
            '<div><em>Using an existing inbox account? Please contact <a href="mailto:wordpress@inbox.zendesk.com">wordpress@inbox.zendesk.com</a></em></div>';
  }

  /**
   * Returns the show field content
   */
  public function validate_show_setting( $input ) {
    return (int) $input;
  }

  /**
   * Loads the css and js files to the admin pages
   */
  public function load_css_and_js() {
    // register but don't load the styles
    wp_register_style( 'zendesk-inbox-admin', Zendesk_Inbox_Utilities::get_css_file_url( 'inbox_zendesk' ), array(), INBOX_ZENDESK_VERSION, 'screen' );

    wp_register_script( 'zendesk-inbox-admin', Zendesk_Inbox_Utilities::get_js_file_url( 'admin' ), array( 'jquery' ), INBOX_ZENDESK_VERSION, true );
    wp_enqueue_script( 'zendesk-inbox-admin' );
  }

  /**
   * Fires after the subdomain option is updated. Only triggers when the value is changed.
   */
  public function after_subdomain_update() {
    // Refresh the widget code
    delete_option( 'zendesk_inbox_widget_snippet' );
  }
}
