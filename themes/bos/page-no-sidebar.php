<?php
/*

Template Name: No Sidebar

*/
?>
<?php get_header(); ?>
<div id="main" class="wide">
  <?php
  
  if (have_posts()) {
		while (have_posts()) {
			the_post(); 
			get_template_part('post');
		}
	}
	
	?>
</div>
<br class="clear">
<?php get_footer(); ?>
