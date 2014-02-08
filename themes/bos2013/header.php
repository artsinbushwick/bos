<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged, $aib_page_title;

	if (!empty($aib_page_title)) {
	  echo $aib_page_title;
	} else {
    wp_title( '|', true, 'right' );
  
    // Add the blog name.
    bloginfo( 'name' );
  
    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
      echo " | $site_description";
  
    // Add a page number if necessary:
    if ( $paged >= 2 || $page >= 2 )
      echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );
  }

	?></title>
	
<!-- Load JQuery -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>	

<!-- Random Background -->

<script language="JavaScript">
	var randnum = Math.random();
 	var inum = 14;
 	var rand1 = Math.round(randnum * (inum-1)) + 1;
  	images = new Array
  	images[1] = "/wp-content/themes/bos2013/images/headers/header1.jpg"
  	images[2] = "/wp-content/themes/bos2013/images/headers/header2.jpg"
  	images[3] = "/wp-content/themes/bos2013/images/headers/header3.jpg"
  	images[4] = "/wp-content/themes/bos2013/images/headers/header4.jpg"
  	images[5] = "/wp-content/themes/bos2013/images/headers/header5.jpg"
  	images[6] = "/wp-content/themes/bos2013/images/headers/header6.jpg"
  	images[7] = "/wp-content/themes/bos2013/images/headers/header7.jpg"
  	images[8] = "/wp-content/themes/bos2013/images/headers/header8.jpg"
  	images[9] = "/wp-content/themes/bos2013/images/headers/header9.jpg"
  	images[10] = "/wp-content/themes/bos2013/images/headers/header10.jpg"
  	images[11] = "/wp-content/themes/bos2013/images/headers/header11.jpg"
  	images[12] = "/wp-content/themes/bos2013/images/headers/header12.jpg"
  	images[13] = "/wp-content/themes/bos2013/images/headers/header13.jpg"
  	images[14] = "/wp-content/themes/bos2013/images/headers/header14.jpg"
	var image = images[rand1]
</script>

<!-- Share This -->
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher:'9789cb03-3faa-44a7-bf38-f5e1fc4154c2'});</script>
	
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

  wp_head();
?>

</head>

<script language="JavaScript">
	document.write('<body style="background-image:url(' + image + ');" text="white" <?php body_class(); ?>>')
</script>

<div id="wrapper" class="hfeed">

  <!-- #header -->
	<div id="header">
	  
	  <div class="head_aib">
	    <div class="head_center">
	      <a href="/bos2013"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/title_aib.png" alt="Arts in Bushwick" /></a>
	    	<ul class="nav_aib">
		  <li><a href="/bos2013/about">About Arts in Bushwick</a></li>
		  <li><a href="/bos2013/contact">Contact</a></li>
		  <li><a href="/bos2013/press">Press</a></li>
<li><a href="http://blog.artsinbushwick.org/tagged/aib-radio">Radio</a></li>	
	    	  <li><a href="http://blog.artsinbushwick.org">Blog</a></li>	    	  
	    	</ul>
	    </div>
	  </div>
	  <div class="head_bos">
	    <div class="head_center">
	      <a href="/bos2013">
	        <img src="<?php bloginfo('stylesheet_directory'); ?>/images/spacer.gif" alt="Bushwick Open Studios" style="width: 960px; height: 70px" />
	      </a>
	    </div>	  
	  </div>
	  <div class="nav_bos">
	    <div class="head_center">
	      <ul class="head_bos_nav">
	    	  <li class="fifth">&nbsp;Celebrating 7 Years!</li>
	    	  <li class="date">May 31-June 2 &bull;</li>
	    	  <li><a href="/bos2013/register">Registration</a></li>
	    	  <li><a href="/bos2013/seeking">Artists Seeking</a></li>
		<li><a href="/bos2013/opencalls/">Open Calls</a></li>
		<li><a href="/bos2013/aib-benefit-2013/">Benefit</a></li>
              <li style="display: none"><a href="/bos2011/benefit">Benefit</a></li>
	    	  <li><a href="/bos2013/join-us">Volunteer</a></li>
	    	  <li><a href="/bos2013/espanol">Espa&ntilde;ol</a></li>
	    	  <li><a href="/bos2013/directory">Map & Directory</a></li>
          	  <li style="display: none"><a href="/bos2013/benefit">Benefit</a></li>
	    	  <li><a href="/bos2013/sponsor">Sponsorship</a></li>
	    	</ul>
	    </div>
	  </div>
	  
	</div>

	<div id="main">