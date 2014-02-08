<?php
/*
Template Name: Bios Page
*/

get_header(); ?>
      
		<div id="container">

			<div id="content" role="main">

			<?php
			/* Run the loop to output the page.
			 * If you want to overload this in a child theme then include a file
			 * called loop-page.php and that will be used instead.
			 */
			get_template_part( 'loop', 'page' );
			?>
			
			<?php $bio_query = new WP_Query('posts_per_page=100&post_type=bio&orderby=menu_order&order=ASC');
				while ($bio_query->have_posts()) : $bio_query->the_post();
			?>
			<div class="bio-result">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('bio-thumb', array('title' => '') ); ?></a>
				<header class="bio-head">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</header>
			</div>
			<?php endwhile; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php include( TEMPLATEPATH . '/sidebar_bos.php' ); ?>

<?php get_footer(); ?>
