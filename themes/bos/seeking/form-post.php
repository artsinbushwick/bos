<?php

global $bos;
$seeking = $bos->seeking;

?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
  <input type="hidden" name="task" value="post_seeking" />
  <fieldset id="submit-form" class="headroom">
    <legend>Create a New Listing</legend>
    <label>
      <?php $seeking->radio_input('type', 'artist', array('checked' => (v('type') == 'artist'))); ?>
      Artist Seeking Space
    </label>
    <label>
      <?php $seeking->radio_input('type', 'space', array('checked' => (v('type') == 'space'))); ?>
      Space (or Show) Seeking Artists
    </label>
    <label>
      Your name
      <?php $seeking->text_input('seeking_name', v('seeking_name')); ?>
    </label>
    <label>
      Email address (not listed publicly)
      <?php $seeking->text_input('email', v('email')); ?>
    </label>
    <label>
      Website (if applicable)
      <?php $seeking->text_input('url', v('url')); ?>
    </label>
    <label>
      <strong>Description</strong> <span class="help">a brief description of yourself or your space/show</span>
      <br />i.e., "A photography show on the theme of food" or "I am a
photographer working on a project photographing food"<br />
      <span class="required">100 words or fewer please</span>
      <?php $seeking->textarea_input('description', v('description')); ?>
    </label>
    <label>
      <strong>Seeking...</strong> <span class="help">what you are looking for?</span><br />
      i.e., "seeking two more photographers" or "seeking a space to show 10 photographs"</span><br />
      <span class="required">100 words or fewer please</span>
      <?php $seeking->textarea_input('seeking', v('seeking')); ?>
    </label>
    <label>
      <strong>Spambot challenge:</strong> what <i>neighborhood</i> in New York City does BOS take place in? (Hint: it's in the name of the event.)
      <?php $seeking->text_input('spambot', v('spambot')); ?>
    </label>
    <input type="submit" value="Post it!" class="button" />
  </fieldset>
</form>
