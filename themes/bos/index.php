<?php get_header(); ?>
<div id="main">
  <?php
  
  if (have_posts()) {
		while (have_posts()) {
			the_post(); 
			?>
			<div id="post-<?php the_ID(); ?>" class="post">
			  <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			  <div class="content">
			    <?php the_content(); ?>
			  </div>
			  <?php edit_post_link('Edit post'); ?>
			</div>
			<?php
		}
	}
	
	?>
</div>
<?php get_sidebar(); ?>
<br class="clear">
<?php get_footer(); ?>