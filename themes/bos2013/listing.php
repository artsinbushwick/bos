<?php 

global $aib_post_title;
$post = aib_directory_get_post($_GET['listing']);
$aib_page_title = "$post->post_title | " . get_bloginfo('name');

?>
<?php get_header(); ?>
<div id="listing">
  <a href="./" class="back_to">&laquo; Back to BOS Directory</a>
  <h4><?php aib_primary_name($post); ?></h4>
  <h5><?php aib_subtitle($post); ?></h5>
  <ul class="column">
    <li class="location">
      <?php aib_show_map($post); ?>
      <h5>Location</h5>
      <?php aib_show_location($post); ?>
    </li>
    <li class="date_time">
      <h5>Dates &amp; Times</h5>
      <?php aib_show_times($post); ?>
      <?php aib_admission($post); ?>
    </li>
    <li class="media">
      <h5>Media</h5>
      <?php aib_show_media($post); ?>
    </li>
    <li class="features">
      <h5>Features</h5>
      <?php aib_show_features($post); ?>
    </li>
    <li>
       <h5><?php aib_number_of_participants($post); ?></h5>
    </li>
    <li class="links">
      <?php aib_show_links($post); ?>
    </li>
  </ul>
  <div class="images"><?php aib_images($post); ?></div>
  <div class="content"><?php aib_content($post); ?></div>
  <span  class='st_facebook_button' displayText='Facebook'></span><span  class='st_twitter_button' displayText='Tweet'></span><span  class='st_instapaper_button' displayText='Instapaper'></span><span  class='st_tumblr_button' displayText='Tumblr'></span><span  class='st_email_button' displayText='Email'></span>
</div>
<br class="clear" />
<?php get_footer(); ?>
