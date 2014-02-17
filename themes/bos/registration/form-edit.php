<?php

global $userdata, $page_slug, $bos;

require_once ABSPATH . 'wp-admin/includes/template.php';

$reg = $bos->registration;
$year = $reg->get_year();

if (!empty($_POST['task']) && $_POST['task'] == 'save' && empty($response)) {
  $response = '<ul><li>Your listing information has been saved.  You may continue to edit your information here.</li><li>If you still need to pay the registration fee, please make sure to scroll down to the Paypal link at the bottom of this form.</li><li>You should have also received an email from us with all of the information you will need to manage your listing, update your information, organize your show, and stay in touch with us.  If you have any questions, feel free to email us at <a href="mailto:registration@artsinbushwick.org">registration@artsinbushwick.org</a>.</li></ul>';
}

?>
<?php if (!empty($response)) { ?>
  <div id="response">
    <?php echo $response; ?>
  </div>
<?php } ?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
  <p id="user">You are signed in as <strong><?php echo $userdata->user_email; ?></strong> <a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>" class="auth">logout</a></p>
  <fieldset id="introduction">
    <legend>Introduction</legend>
    <div id="jump">
      <strong>Registration Sections</strong>
      <ol>
        <li><a href="#basic-info">Basic Show Information</a></li>
        <li><a href="#where-when">Show Details: Where and When</a></li>
        <li><a href="#media-types">Show details: Media Types and Other Features</a></li>
        <li><a href="#description">Description</a></li>
        <li><a href="#images">Images</a></li>
        <li><a href="#preview-benefit">Preview Benefit</a></li>
        <li><a href="#odds-and-ends">Odds and Ends</a></li>
        <li><a href="#contribute">Contribute!</a></li>
        <li><a href="#legal">Terms and Conditions</a></li>
      </ol>
    </div>
    <p>Welcome to the registration form for BOS<?php echo $year; ?>!</p>
    <?php the_content(); ?>
    <p>To register a show for BOS<?php echo $year; ?>, please complete the following steps:</p>
    <ol>
      <li>
        <p>Enter the information about your show. A show can be any artistic or creative activity that you would like to have included in the BOS<?php echo $year; ?> Festival program &mdash; as long as you're doing something within the Festival area <link to map> during the weekend of <?php echo get_theme_mod('bos_friday_date'); ?>-<?php echo get_theme_mod('bos_sunday_date'); ?>, and you want people to come see it, you can go ahead and register here!</p>
        <p>The information you enter will be used to promote your event in our print program and online, so please fill it out as completely and accurately as possible.</p>
        <p>If you don't have all of your information yet that's OK, you can come back and edit this up until the close of registration on April 28th (for the print program), or up until the weekend of BOS (for the web profiles).</p>
        <p>However, if you're not sure whether you're doing a show, or you don't have a space yet, we ask that you please wait to register until you have your plans in place.  If you need space to show during BOS, you can check out the <a href="<?php bloginfo('url'); ?>/seeking/">Artists Seeking Spaces / Spaces Seeking Artists</a> page and see what spaces or group shows are looking for additional artists.</p>
        <p>
      </li>
      <li class="headroom">Upload images. The images you submit will be displayed online as part of your artist profile.</li>
      <li>
        <p>Contribute.  Since BOS<?php echo $year; ?> is an all-volunteer event, all BOS<?php echo $year; ?> registrants are required contribute to making the Festival happen.  You can contribute in one of two ways:</p>
        <ul>
          <li>Pay a registration fee of $35</li>
          <li>Volunteer at least five hours to help with festival operations, either before or during the BOS weekend.</li>
        </ul>
        <p>More information on how to contribute is at the bottom of this form.  PLEASE NOTE that you must finish this section for your registration to be complete.</p>
      </li>
    </ol>
    <p>If you have any questions about registration, please feel free to email us at <a href="mailto:registration@artsinbushwick.org">registration@artsinbushwick.org</a>.  For general questions about participating in BOS<?php echo $year; ?>, please email <a href="mailto:openstudios@artsinbushwick.org">openstudios@artsinbushwick.org</a>.</p>
  </fieldset>
  <h3 class="required">* required fields</h3>
  <fieldset id="basic-info">
    <legend>Basic Show Information</legend>
    <p class="required">* Required: Individual Artist Name(s) OR Organization Name OR Event Title</p>
    <label>
      Individual Artist Name(s) <span class="required">*</span> <span class="help">comma-separated</span>
      <?php $reg->text_input('artists', ''); ?>
    </label>
    <label>
      Organization Name (if applicable) <span class="required">*</span>
      <?php $reg->text_input('organization', ''); ?>
    </label>
    <label>
      Event Title (if applicable) <span class="required">*</span>
      <?php $reg->text_input('event_name', ''); ?>
    </label>
    <label>
      <?php $reg->radio_input('primary_name', 'artists'); ?> This is an art studio listing. Make <em>artist name(s)</em> the primary name for your listing.
    </label>
    <label>
      <?php $reg->radio_input('primary_name', 'event_name'); ?> This is an exhibition. Make <em>event title</em> the primary name for your listing.
    </label>
    <hr>
    <label>
      Website <span class="help">comma separate to list more than one</span>
      <?php $reg->text_input('website', ''); ?>
    </label>
    <label>
      Total number of participants in your show.
      <?php $reg->text_input('artist_count', ''); ?>
      <p>Please include all artists, performers, organizers, and anyone else
whose work will be shown or who is part of making your show happen.</p>
    </label>
  </fieldset>
  <fieldset id="where-when">
    <legend>Show Details: Where and When</legend>
    <p>Please enter the location where you will be showing during BOS<?php echo $year; ?>.  DO NOT enter your home address, or any other location.  If your location is not within the <a href="http://maps.google.com/maps/ms?ie=UTF&msa=0&msid=100857039057171602899.000467660590f1726e3fb">BOS<?php echo $year; ?> festival area</a>, your listing will not be included on our program or web listings.</p>
    <div class="form-field">
      <div class="controls float">
        <label>
          Space Name (if applicable)
          <?php $reg->text_input('space_name'); ?>
        </label>
      </div>
      <div class="info float">
        <p>Please enter the name of your space if appropriate, i.e. the name of a building, gallery, business, public space, etc.  Please do not re-enter your own name or your street address.  Examples: 'Brooklyn Public Library' or 'outdoors on the corner of Main Street and Broadway.'</p>
      </div>
      <br class="clear" />
    </div>
    <div class="controls float">
      <label>
        Street Address <span class="required">*</span>
        <span class="help">Ex: 476 Jefferson St.</span>
        <?php $reg->text_input('street_address'); ?>
      </label>
    </div>
    <div class="info float">
      <p><strong>Please enter a valid street address.</strong> If your show is taking place outdoors or at a location that doesn't have a street address, please enter the nearest valid street address available so that we can place you on the Festival map.</p>
    </div>
    <br class="clear" />
    <div class="form-field float">
      <label>
        Apartment/Suite/Room <span class="help">Ex: #202, #4R, Ground Floor</span>
        <?php $reg->text_input('room_number'); ?>
      </label>
      <label>
        Zip Code <span class="required">*</span>
        <?php $reg->text_input('zip_code'); ?>
      </label>
    </div>
    
    <br class="clear" />
    <hr />
    <div class="when form-field">
      <p>Please select the date(s) and time(s) of your show.</p>
      <div class="controls float">
        <div class="form-field">
          <label class="day"><?php $reg->checkbox_input('select_friday'); ?> <strong>Friday, <?php echo get_theme_mod('bos_friday_date'); ?></strong> <span class="required">*</span></label>
          <label class="time">
            Open Hours:
            <?php $reg->text_input('time_friday', ''); ?>
          </label>
          <label class="time">
            Additional Times:
            <?php $reg->text_input('additional_time_friday', ''); ?>
          </label>
        </div>
        <label class="day"><?php $reg->checkbox_input('select_saturday'); ?> <strong>Saturday, <?php echo get_theme_mod('bos_saturday_date'); ?></strong> <span class="required">*</span></label>
        <label class="time">
          Open Hours:
          <?php $reg->text_input('time_saturday', ''); ?>
        </label>
        <label class="time">
          Additional Times:
          <?php $reg->text_input('additional_time_saturday', ''); ?>
        </label>
        <label class="day"><?php $reg->checkbox_input('select_sunday'); ?> <strong>Sunday, <?php echo get_theme_mod('bos_sunday_date'); ?></strong> <span class="required">*</span></label>
        <label class="time">
          Open Hours:
          <?php $reg->text_input('time_sunday', ''); ?>
        </label>
        <label class="time">
          Additional Times:
          <?php $reg->text_input('additional_time_sunday', ''); ?>
        </label>
      </div>
      <div class="info float">
        <p>BOS<?php echo $year; ?> begins Friday evening May 31st and runs through Sunday evening June 2nd.  Standard Festival hours are 12-7 Saturday and Sunday, but you can be open at whatever time and for whatever hours you would like.</p>
        <p>"Open Hours" reflects the full hours your space will
be open that day.</p>
        <p>"Additional Times" should reflect any special
time-specific event or activity happening within your Open Hours.
I.e., if your show will be open from 12pm-7pm AND you are having an artist
talk from 3pm-5pm, enter "3pm" or "3pm-5pm" in this field.  Please enter
ONLY the time of your time-specific activity - do not enter additional
description of your time-specific activity here - you can include this
information in your show description below.  Do not enter anything in
this field if your event is at the same times as your total open hours
for this day.</p>
      </div>
      <br class="clear" />
      <p class="required">* Required: At least one day/time</p>
    </div>
  </fieldset>
  <fieldset id="media-types">
    <legend>Show details: Media Types and Other Features</legend>
    <div class="info float">
      <p>Please select up to 5 media types for your show.  These will help visitors find the type of work that they are most interested in seeing, and will make it easier for them to find you.</p>
    </div>
    <div class="float">
      <h3>Media Types <span class="required">*</span></h3>
      <div id="media" class="taxonomy">
        <ul>
          <?php
          
          wp_terms_checklist($post->ID, array(
            'taxonomy' => 'media',
            'checked_ontop' => false
          ));
          
          ?>
        </ul>
      </div>
      <p class="required">* Required: At least one media type</p>
      <div id="media-other"><?php $reg->text_input('media_other', ''); ?></div>
    </div>
    <div class="float nudge-right">
      <h3>Other Features - My show:</h3>
      <div id="attributes" class="taxonomy">
        <ul>
          <?php
          
          wp_terms_checklist($post->ID, array(
            'taxonomy' => 'attributes',
            'checked_ontop' => false
          ));
          
          ?>
        </ul>
      </div>
    </div>
    <br class="clear" />
    <hr />
    <div class="form-field admission">
      <div class="controls float">
        <label>
          <?php $reg->radio_input('admission_free', '1'); ?>
          <span class="text">Free Event</span>
        </label>
        <p class="or"><strong>OR</strong></p>
        
          <span class="text">
          <?php $reg->radio_input('admission_free', '0'); ?>
          Admission Price:</span>
          <?php $reg->text_input('admission_price', ''); ?>
        
        <br class="clear">
      </div>
      <div class="info float">
        <p><strong>ADMISSION PRICE:</strong> Note that most events during BOS are free, but registrants have the option to charge admission if they choose.</p>
      </div>
      <br class="clear">
    </div>
  </fieldset>
  <fieldset id="description">
    <legend>Description</legend>
    <label>
      Short description <span class="required">*</span> <span class="help" id="excerpt_length">140 characters left, will be part of your show listing on the print program</span>
      <?php $reg->textarea_input('post_excerpt', '', array('maxlength' => 140)); ?>
    </label>
    <label>
      Full description <span class="help">any length, will be visible online only</span>
      <?php $reg->textarea_input('long_description', '', array('rows' => 10)); ?>
    </label>
  </fieldset>
  <fieldset id="images">
    <legend>Images</legend>
    <iframe src="<?php bloginfo('url'); ?>/<?php echo $page_slug; ?>/?images=1" name="images" id="images" border="0"></iframe>
  </fieldset>
  <fieldset id="preview-benefit">
    <legend>Preview Benefit</legend>
    <h3>Donate a piece of artwork to the Benefit showcase.</h3>
    <p>Arts In Bushwick will be holding a benefit event for BOS in the third week of May, which will include an affordable art sale.  All BOS registered artists are invited to donate a piece to be sold at the benefit, which will also give attendees the opportunity to preview their work.  Would you be interested in participating?</p>
    <label class="benefit"><?php $reg->checkbox_input('benefit_interest'); ?>
    <strong>Yes, I would like to participate in the benefit showcase!</strong> (We will contact you soon with additional information.)</label>
  </fieldset>
  <fieldset id="odds-and-ends">
    <legend>Odds and Ends</legend>
    <label>
      How did you hear about us?
      <?php $reg->text_input('referral'); ?>
    </label>
    <hr />
    <p>Are you interested in being interviewed for a profile story on the BOS<?php echo $year; ?> Blog?  We will not be able to interview everyone, but we will select subjects at random from everyone who is interested.</p>
    <label class="profile">
      <?php $reg->checkbox_input('profile_story'); ?>
      <strong>Yes I am interested in being profiled</strong>
    </label>
  </fieldset>
  <fieldset id="contribute">
    <legend>Contribute!</legend>
    <p>Since BOS<?php echo $year; ?> is an all-volunteer event, all BOS<?php echo $year; ?> registrants are required contribute to making the Festival happen.  You can contribute in one of two ways:</p>
    <ul>
      <li>Pay a registration fee of $35 OR</li>
      <li>Volunteer at least five hours to help with festival operations, either before or during the BOS weekend.</li>
      <!--<li>Donate a piece of artwork to be sold at the BOS<?php echo $year; ?> Benefit Showcase and Sample Sale.</li>-->
    </ul>
    <p>PLEASE NOTE: If you offer to volunteer but if for any reason you do not fulfill your five-hour
obligation, you will be billed for the registration fee.  If you do
not pay this outstanding bill, you will not be allowed to register for
future Arts In Bushwick events.</p>
    <hr />
    <div class="method float">
      <h3>1. Pay $35 registration fee</h3>
      <p id="payment-info">Once you have completed and saved your listing information, please submit payment by check or credit card via Paypal using the button at the bottom of this page.  If you are unable to pay by check or credit card, please contact us at <a href="mailto:registration@artsinbushwick.org">registration@artsinbushwick.org</a> for instructions on paying in cash.</p>
    </div>
    <p class="or float"><strong>OR</strong></p>
    <div class="method float">
      <h3>2. Volunteer 5 hours to help with Festival operations</h3>
      <p>
        Please describe
your availability before and during the BOS weekend, and please be <em>as
flexible as possible.</em>  If you have any special skills or interests in
press and promotions, event production, etc, please let us know.
      </p>
      <?php $reg->textarea_input('volunteer_message'); ?>
      <p>Based on the information
above, you will receive an email from our Volunteer Coordinator to set
up your volunteer slot. Your registration will not be complete until
you have corresponded with the Volunteer Coordinator and have
committed to a specific volunteer slot. If you do not hear from us or
want to contact us directly about volunteering, please email <a href="mailto:volunteer@artsinbushwick.org">volunteer@artsinbushwick.org</a>.</p>
    </div>
    <br class="clear" />
    <!--<hr />-->
    <!--<p class="or"><strong>OR</strong></p>
    -->
    <p class="required">* Required: you must choose one contribute option</p>
  </fieldset>
  <fieldset id="legal">
    <legend>Terms and Conditions</legend>
    <label>
      <?php $reg->checkbox_input('legal_agreement'); ?>
      <strong>I have read and accept the Terms and Conditions</strong>
    </label>
    <a href="<?php bloginfo('url'); ?>/register/terms-and-conditions/" target="_blank">Read the BOS<?php echo $year; ?> Registration Terms and Conditions</a>
    <hr />
    <label>
      <?php $reg->checkbox_input('community_agreement'); ?>
      <strong>I understand that Arts In Bushwick is an all-volunteer community organization, and
      that as a registrant I will share responsibility for making BOS<?php echo $year; ?> a
      success.</strong>
    </label>
    <a href="<?php bloginfo('url'); ?>/register/registrant-how-to/" target="_blank">Read the BOS<?php echo $year; ?> Registrant How-To</a>
  </fieldset>
  <fieldset id="save">
      <legend>Save Listing!</legend>
      <p>You must click this button to submit your listing information!  If you do not click this button, your information will not be saved!</p>
  <input type="submit" value="Save listing" class="button" />
  </fieldset>

<!---

  <p id="status">
    <strong>Your listing status</strong>: <em><?php
  
    if ($post->post_status == 'publish') {
      echo 'Published!';
    } else if (!$reg->is_complete()) {
      echo 'Not yet published.';
    } else {
      echo 'Thank you for submitting your registration! Your registration is currently being processed, but you may continue to edit your information here.  You should have also received an email from us with all of the information you will need to manage your listing, update your information, organize your show, and stay in touch with us.  If you have any questions, feel free to email us at <a href="mailto:openstudios@artsinbushwick.org">openstudios@artsinbushwick.org</a>.';
    }
    
    ?></em>
  </p>

--->

</form>
<?php if (!$post->squared_up) { ?>
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <fieldset>
	    <legend>Contribute using PayPal</legend>
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="RKLVWPLXZ8S6G">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </fieldset>
  </form>
<?php } else { ?>
<form action="#" id="paypal">
  <fieldset>
    <legend>Contribute using PayPal</legend>
    Your payment has been received!
  </fieldset>
</form>
<?php } ?>
