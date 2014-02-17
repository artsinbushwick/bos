<?php

global $aib_login_response, $bos;
$reg = $bos->registration;

?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="form-login">
<input type="hidden" name="task" value="login" />
  <?php if (!empty($aib_login_response)) { ?>
    <div id="response">
      <?php echo $aib_login_response; ?>
    </div>
  <?php } ?>
  <fieldset>
    <legend><?php bloginfo('name'); ?></legend>
    <div class="fields">
      <label>
        <?php $reg->radio_input('action', 'create', array('checked' => (v('action') != 'edit'))); ?>
        Start a new listing
      </label>
      <label>
        <?php $reg->radio_input('action', 'edit', array('checked' => (v('action') == 'edit'))); ?>
        Edit an existing listing
      </label>
      <label>
        Email address
        <?php $reg->text_input('email', v('email')); ?>
      </label>
      <label>
        Password
        <?php $reg->text_input('password', v('password'), array('type' => 'password')); ?>
      </label>
      <div id="register_options">
        <label id="confirm_password">
          Confirm Password
          <?php $reg->text_input('confirm_password', v('confirm_password'), array('type' => 'password')); ?>
        </label>
        <label id="token">
          Registration code <a href="<?php echo bloginfo('url'); ?>/registration-codes/">what is this?</a>
          <?php $reg->text_input('token', v('token')); ?>
        </label>
      </div>
    </div>
    <div class="info">
      <p><strong>If you would like to register for BOS <?php echo $reg->get_year(); ?> using and email address that you used for a prior year’s listing:</strong> You will need to Start a New Listing, using the same password you used last year.  If you have forgotten that password, you will need to <a href="/wp-login.php?action=lostpassword" class="auth">reset your password</a>.  You will receive an email from the WordPress system, with a link to follow to reset your password.  Once you have reset your password in the WordPress system, you may return here to create your new BOS <?php echo $reg->get_year(); ?> listing.  If you have difficulty resetting your password, feel free to contact us at <a href="mailto:registration@artsinbushwick.org">registration@artsinbushwick.org</a>.</p>
    </div>
    <br class="clear">
    <input type="submit" value="Register" id="submit_register" class="button" />
    <input type="submit" value="Login" id="submit_login" class="button" />
  </fieldset>
  <p><a href="/wp-login.php?action=lostpassword" class="auth">Forget your password?</a></p>
</form>
