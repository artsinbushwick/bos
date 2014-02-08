<?php
/*
Template Name: Pre-festival Home
*/
?>

<?php get_header(); ?>
      
  
    <div id="container">
		
		  <h2 class="entry-title"><a href="/bos2013/directory">BOS2013 Show Directory & Map &raquo;</a></h2>
		  <p class="count">6 Random profiles from 600+ Shows</p>
		
      <ul class="artistThumbs">
        <?php $posts = aib_get_posts(array(
          'require_images' => true,
          'post_count' => 6
        )); ?>
        <?php foreach ($posts as $post) { ?>
          <li class="exhibit">
            <a href="http://artsinbushwick.org/bos2013/directory/?listing=<?php echo $post->ID; ?>">
              <?php aib_thumbnail($post, true); ?>
              <div class="exhibitInfo">
                <h3 class="exhibitTitle">
                  <?php aib_primary_name($post); ?>
                </h3>
                <div class="exhibitSubtitle">
                  <?php aib_subtitle($post); ?>
                </div>
              </div>
            </a>
          </li>
        <?php } ?>
      </ul>

			<div id="content" role="main">

		<?php
      
		      // The Query
    		  $the_query = new WP_Query( 'page=39' );

		      // The Loop
    		  while ( $the_query->have_posts() ) : $the_query->the_post();
 	      		echo '<h2 class="entry-title">';
	      		the_title();
 	      		echo '</h2>';
 	      		echo '<div class="entry-content">';
	      		the_content();
	      		echo '</div>';
      		endwhile;

		      // Reset Post Data
   		 wp_reset_postdata(); ?>
      </div><!-- #content -->
		</div><!-- #container -->

<?php include( TEMPLATEPATH . '/sidebar_bos.php' ); ?>

<?php get_footer(); ?>
