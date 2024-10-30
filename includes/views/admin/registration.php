<?php
/**
 * The view file for the registration page. Display the form to register to inbox.
 */
?>
<div class="wrap" id="inbox-registration-form">
  <h2><?php _e( 'Work together on shared email.', 'zendesk-inbox' ); ?></h2>
  <div class="container">

    <div class="description">
      <p><?php _e( 'Inbox by Zendesk helps your team manage emails together. It lets you talk among yourselves, respond as one, and be there for your people.', 'zendesk-inbox' ); ?></p>
      <p><?php _e( 'Take Inbox for a test drive and once inside your new Inbox, we’ll set up forwarding for support@, or sales@, or any other team email alias.', 'zendesk-inbox' ); ?></p>
    </div>

    <div id="sign-up-container">
      <?php if ( isset( $error_message ) ): ?>
        <div class="error settings-error">
          <p><strong><?php _e( $error_message ); ?></strong></p>
        </div>
      <?php endif; ?>
      <form accept-charset="UTF-8" action="<?php echo esc_url( $_SERVER[ 'REQUEST_URI' ] ); ?>" class="pull-left"
            id="new_zendesk_api_account" method="post">
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row"><?php _e( 'Email', 'zendesk-inbox' ); ?></th>
              <td>
                <?php
                  $current_user = wp_get_current_user();
                  echo $current_user->user_email;
                ?>
              </td>
            </tr>
            <tr>
              <th scope="row"><?php _e( 'Password', 'zendesk-inbox' ); ?></th>
              <td>
                <input autocomplete="off" class="form-control" id="zendesk_api_account_owner_password"
                       name="zendesk_api_account[owner_password]" placeholder="<?php _e( 'Choose Password', 'zendesk-inbox' ); ?>" type="password">
              </td>
            </tr>
            <tr>
              <th scope="row"><?php _e( 'Company Name', 'zendesk-inbox' ); ?></th>
              <td>
                <input class="form-control" id="zendesk_api_account_name" name="zendesk_api_account[name]"
                       placeholder="<?php _e( 'Your company', 'zendesk-inbox' ); ?>" required="required" type="text">
              </td>
            </tr>
            <tr>
              <th scope="row"><?php _e( 'Subdomain', 'zendesk-inbox' ); ?></th>
              <td>
                <input autocomplete="off" class="form-control last-input" id="zendesk_api_account_subdomain"
                       name="zendesk_api_account[subdomain]" placeholder="<?php _e( 'subdomain', 'zendesk-inbox' ); ?>" required="required" type="text">
                <span class="input-group-addon">.<?php echo INBOX_ZENDESK_DOMAIN; ?></span>
              </td>
            </tr>
          </tbody>
        </table>
        <p class="submit">
          <input class="button-primary" name="commit" type="submit" value="Sign Up">
          <span class="forever-free"><?php _e( 'Forever free when you sign up during the beta', 'zendesk-inbox' ); ?></span>
        </p>

        <p>
          <?php _e( 'By clicking "Sign Up" you agree to the', 'zendesk-inbox' ); ?>
          <a href="https://www.zendesk.com/company/terms#inbox-terms" target="_blank"><?php _e( 'Inbox by Zendesk Terms of Service', 'zendesk-inbox' ); ?></a>
          <?php _e( 'and', 'zendesk-inbox' ); ?>
          <a href="https://www.zendesk.com/company/privacy" target="_blank"><?php _e( 'Privacy Policy', 'zendesk-inbox' ); ?></a>.
        </p>
        </div>
      </form>
    </div>
  </div>
</div>
