<?php
/*
Template Name: Bios Page
*/

get_header(); ?>
<div id="main">
  <?php
  
  if (have_posts()) {
    while (have_posts()) {
      the_post();
      get_template_part('post');
    }
  }
  
  ?>
  <div class="bios">
    <?php
    $bio_query = new WP_Query(array(
      'posts_per_page' => -1,
      'post_type' => 'bio',
      'orderby' => 'menu_order',
      'order' => 'ASC'
    ));
    
    while ($bio_query->have_posts()) {
      $bio_query->the_post();
      ?>
      <a href="<?php the_permalink(); ?>" class="bio">
        <?php the_post_thumbnail('bio-thumb', array('title' => '') ); ?>
        <span class="hover">
          <?php the_title(); ?>
        </span>
      </a>
    <?php } ?>
    <br class="clear">
  </div>
</div>
<?php get_sidebar(); ?>
<br class="clear">
<?php get_footer(); ?>
