<?php

class Blog_Test extends WP_UnitTestCase {

  /**
   * Tests if the snippet is appended to the footer.
   * Does NOT test if the widget is visible on the browser.
   */
  function test_snippet_generation() {
    $api = new Zendesk_Inbox_API();

    update_option( 'zendesk_inbox_show', 1 );
    update_option( 'zendesk_inbox_subdomain', INBOX_ZENDESK_SUBDOMAIN );

    ob_start();
    do_action( 'wp_footer' );
    $footer = ob_get_clean();

    $widget_code = $api->get_widget_code();

    $this->assertContains( $widget_code, $footer, 'The widget code was not added to the footer.' );
  }
}
