<div class="wrap">
  <h2><?php _e( 'Inbox by Zendesk', 'zendesk-inbox' ); ?></h2>

  <form action="options.php" method="post">
    <?php
    settings_fields( 'zendesk_inbox_settings' );
    do_settings_sections( 'inbox-by-zendesk' );
    submit_button();
    ?>
  </form>
</div>
