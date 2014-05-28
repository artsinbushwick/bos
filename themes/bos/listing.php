<?php

global $wpdb, $post, $blog_id;

$blog_id = 10;

$listings = $wpdb->get_results($wpdb->prepare("
  SELECT *
  FROM aib_listing
  WHERE post_id = %d
    AND site_id = %d
", $post->ID, $blog_id));
if (!empty($listings)) {
  foreach (get_object_vars($listings[0]) as $key => $value) {
    $post->$key = $value;
  }
}

?>
<div id="post-<?php the_ID(); ?>" class="post listing">
  <a href="<?php bloginfo('url'); ?>/directory/" class="back_to">&laquo; Back to BOS Directory</a>
  <div class="title">
    <h3><a href="<?php the_permalink(); ?>"><?php aib_primary_name($post); ?></a></h3>
    <h5><?php aib_subtitle($post); ?></h5>
  </div>
  <div class="location"><?php aib_show_location($post); ?></div>
  <div class="images"><?php aib_images($post); ?></div>
  <div id="main">
    <?php aib_show_times($post); ?>
    <?php aib_admission($post); ?>
    <div class="content"><?php aib_content($post); ?></div>
    <ul class="attributes">
      <li><?php aib_show_links($post); ?></li>
      <li><?php aib_show_media($post); ?></li>
      <li><?php aib_show_features($post); ?></li>
      <li><?php aib_number_of_participants($post); ?></li>
    </ul>
  </div>
  <?php get_sidebar(); ?>
  <br class="clear">
</div>
