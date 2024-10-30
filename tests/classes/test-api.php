<?php

class API_Test extends WP_UnitTestCase {
  /**
   * Tests account creation
   */
  function test_create_account() {
    $api = $this->getMockBuilder( 'Zendesk_Inbox_API' )
                ->setMethods( array( '_send_request' ) )
                ->getMock();

    $api->method( '_send_request' )
        ->will( $this->returnArgument(1) );

    $response = $api->create_account( 'MI-unit-test', INBOX_ZENDESK_SUBDOMAIN, 'MI unit test', 'mi@test.com' );

    // Verify that we're sending the json body
    $this->assertTrue( is_string( $response[ 'body' ] ) && is_object( json_decode( $response[ 'body' ] ) ) );
  }

  /**
   * Tests if an embed code can be retrieved
   */
  function test_get_widget_code()
  {
    delete_option( 'zendesk_inbox_widget_snippet' );
    update_option( 'zendesk_inbox_subdomain', INBOX_ZENDESK_SUBDOMAIN );

    $api = new Zendesk_Inbox_API();

    $widget_code = $api->get_widget_code();
    $is_wp_error = is_wp_error( $widget_code );
    $this->assertFalse( $is_wp_error, ( $is_wp_error ) ? $widget_code->get_error_message() : '' );
    $this->assertContains( INBOX_ZENDESK_SUBDOMAIN, $widget_code, 'Subdomain was not substituted.' );
  }
}
