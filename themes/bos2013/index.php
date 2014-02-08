<?php get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<?php get_template_part( 'loop', 'index' ); ?>
			</div><!-- #content -->
		</div><!-- #container -->

<?php include( TEMPLATEPATH . '/sidebar_bos.php' ); ?>
<?php get_footer(); ?>
