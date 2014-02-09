<?php

global $bos;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php echo $bos->page_title(); ?></title>
    <link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body>
    <header>
      <div class="top">
        <div class="container">
          <h2><a href="<?php bloginfo('url'); ?>">Arts in Bushwick</a></h2>
          <nav class="secondary">
            <?php wp_nav_menu(array('theme_location' => 'secondary-menu')); ?>
          </nav>
          <br class="clear">
        </div>
      </div>
      <div class="bottom">
        <div class="container">
          <h1><a href="<?php bloginfo('url'); ?>">Bushwick Open Studios</a></h1>
          <nav class="primary">
            <a href="#" class="toggle"><img src="<?php echo get_template_directory_uri(); ?>/images/menu.png" width="40" height="27" alt="Menu"></a>
            <?php wp_nav_menu(array('theme_location' => 'primary-menu')); ?>
            <br class="clear">
          </nav>
          <div class="tagline">
            <?php echo str_replace('|', '<br>', get_bloginfo('description')); ?>
          </div>
          <br class="clear">
        </div>
      </div>
    </header>
    <div class="container">
