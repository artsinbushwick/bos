<?php

global $response, $bos;
$reg = $bos->registration;

?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="form-login">
<input type="hidden" name="task" value="login" />
  <?php if (!empty($response)) { ?>
    <div id="response">
      <?php echo $response; ?>
    </div>
  <?php } ?>
  <fieldset>
    <legend><?php bloginfo('name'); ?></legend>
    <div class="fields">
      <label>
        <?php $reg->radio_input('action', 'create', array('checked' => (v('action') == 'create'))); ?>
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
      <?php the_content(); ?>
    </div>
    <br class="clear">
    <input type="submit" value="Register" id="submit_register" class="button" />
    <input type="submit" value="Login" id="submit_login" class="button" />
  </fieldset>
  <p><a href="/wp-login.php?action=lostpassword" class="auth">Forget your password?</a></p>
</form>
