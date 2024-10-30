<?php

/**
 * Handles the API calls to the Zendesk API. see `https://developer.zendesk.com/` for more info.
 *
 * @since      0.2
 * @package    Inbox by Zendesk
 * @subpackage Inbox by Zendesk/includes
 * @author     support@zendesk.com
 */
class Zendesk_Inbox_API
{
  /**
   * Sets the zendesk_inbox_widget_snippet option and returns it as a string.   
   * @return mixed The embeddable code if wp_remote_post does not encounter any errors, else an instance of WP_Error
   */ 
  public function get_widget_code() {
    if ( ! ( $js = get_option( 'zendesk_inbox_widget_snippet' ) ) ) {
      $url = 'https://assets.' . INBOX_ZENDESK_DOMAIN . '/embeddable_framework/bootstrap.js';
      $response = $this->_send_request( $url );

      if ( ! is_wp_error( $response ) ) {
        $body = $response[ 'body' ];
        $subdomain = get_option( 'zendesk_inbox_subdomain' );

        $js = str_replace(
          array( '{{zendeskFrameworkUrl}}', '{{zendeskHost}}' ),
          array( '//assets.' . INBOX_ZENDESK_DOMAIN . '/embeddable_framework/main.js', $subdomain . '.' . INBOX_ZENDESK_DOMAIN ),
          $body
        );

        update_option( 'zendesk_inbox_widget_snippet', $js );
      } else {
        return $response;
      }
    }

    return $js;
  }

  /**
   * Creates an account via the API.
   * @param  string $name        The account name
   * @param  string $subdomain   The subdomain to register the account with
   * @param  string $owner_name  The account owner's name
   * @param  string $owner_email The email address used to create the account
   * @return mixed JSON response from /accounts.json if wp_remote_post does not encounter any errors, else an instance of WP_Error
   */
  public function create_account( $name, $subdomain, $owner_name, $owner_email ) {
    $body = array(
      'account' => array(
        'name'           => $name,
        'subdomain'      => $subdomain,
        'help_desk_size' => 'Inbox',
        'source'         => 'WordPress',
      ),
      'owner'   => array(
        'name'  => $owner_name,
        'email' => $owner_email,
      ),
      'address' => array(
        'phone' => '-',
      ),
    );

    return $this->_send_request( 'https://signup.' . INBOX_ZENDESK_DOMAIN . '/api/v2/accounts.json', array(
      'method'      => 'POST',
      'timeout'     => 30,
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array( 'Content-Type' => 'application/json' ),
      'body'        => json_encode( $body )
    ) );
  }

  /**
   * Wrapper for wp_remote_request.
   * 
   * @param string  $url  The request URL.
   * @param array   $args Optional. Array or string of HTTP request arguments.
   * @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
   *                        A WP_Error instance upon error.
   */
  protected function _send_request( $url, $args = array() ) {
    return wp_remote_request( $url, $args );
  }
}
