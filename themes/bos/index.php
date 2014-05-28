<?php get_header(); ?>
<div id="main">
  <?php
  
  if (have_posts()) {
		while (have_posts()) {
		  the_post(); 
			get_template_part('post');
		}
	} else {
	  echo '<h3>Sorry, nothing was found.</h3>';
	}
	
	?>
</div>
<?php get_sidebar(); ?>
<br class="clear">
<?php get_footer(); ?>