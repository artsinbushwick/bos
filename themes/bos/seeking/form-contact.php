<?php

global $bos, $post;
$seeking = $bos->seeking;
$contact = $seeking->get_contact($_GET['contact']);
$url = get_permalink($post->ID);

?>
<p><a href="<?php echo $url; ?>">Go back to seeking listings</a></p>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="headroom">
  <input type="hidden" name="task" value="contact_seeking" />
  <input type="hidden" name="contact_id" value="<?php echo $contact->id; ?>" />
  <fieldset>
    <legend>Contact <?php echo $contact->name; ?></legend>
    <?php $seeking->feedback(); ?>
    <label>
      Your name
      <?php $seeking->text_input('contact_name', v('contact_name')); ?>
    </label>
    <label>
      Your email
      <?php $seeking->text_input('email', v('email')); ?>
    </label>
    <label>
      Message to send
      <?php $seeking->textarea_input('message', v('message')); ?>
    </label>
    <label>
      <strong>Spambot challenge:</strong> what <i>neighborhood</i> in New York City does BOS take place in?  (Hint: it's in the name of the event.)
      <?php $seeking->text_input('spambot', v('spambot')); ?>
    </label>
    <input type="submit" value="Send" class="button" />
    <hr />
    <h3>Description</h3>
    <?php echo nl2br(esc_html($contact->description)); ?>
    <h3>Seeking...</h3>
    <?php echo nl2br(esc_html($contact->seeking)); ?>
  </fieldset>
</form>
