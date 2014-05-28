<?php get_header(); ?>
<?php

//global $wp_query;

if (have_posts()) {
  while (have_posts()) {
    the_post(); 
    get_template_part('listing');
  }
} else {
  echo '<h3>Sorry, nothing was found.</h3>';
}

?>
<?php get_footer(); ?>
