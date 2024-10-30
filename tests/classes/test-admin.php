<?php

class Admin_Test extends WP_UnitTestCase {

  public function test_create_account() {
      $api = $this->getMockBuilder( 'Zendesk_Inbox_API' )
                  ->setMethods( array( '_send_request' ) )
                  ->getMock();

      $success_response = array(
        'body' => json_encode( array( 'owner_verification_link' => 'loremipsum' ) ),
        'response' => array( 'code' => 200 )
      );

      $error_response = array(
        'body' => json_encode( array( 'description' => 'test error', 'error' => 'test error' ) ),
        'response' => array( 'code' => 400 )
      );

      $post_data = array();
      $post_data[ 'zendesk_api_account' ][ 'name' ] = 'mi-unit-test';
      $post_data[ 'zendesk_api_account' ][ 'subdomain' ] = INBOX_ZENDESK_SUBDOMAIN;

      $api->expects( $this->at( 0 ) )
            ->method( '_send_request' )
            ->willReturn( $success_response );

      $api->expects( $this->at( 1 ) )
            ->method( '_send_request' )
            ->willReturn( $error_response );


      // Create a dummy wp user
      $user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
      wp_set_current_user( $user_id );

      $admin = $this->getMockBuilder( 'Zendesk_Inbox_Admin' )
                    ->setMethods( array( 'display_register_page', 'display_verification_page' ) )
                    ->disableOriginalConstructor()
                    ->getMock();


      $admin->expects( $this->at(0) )
            ->method( 'display_verification_page' );

      $admin->expects( $this->at(1) )
            ->method( 'display_register_page' );

      // Replace protected self reference with mock object
      $ref = new ReflectionProperty( 'Zendesk_Inbox_Admin', 'instance' );
      $ref->setAccessible( true );
      $ref->setValue( null, $admin );

      // Pass our mocked API object as a dependency
      $admin->set_api_client( $api );
      $admin->create_account( $post_data );

      // Check for created account
      $this->assertEquals( INBOX_ZENDESK_SUBDOMAIN, get_option( 'zendesk_inbox_subdomain' ), 'Subdomain was not saved.' );

      // Check how error responses are handled on the second call
      $admin->create_account( $post_data );
  }
}
