<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php echo bos_title(); ?></title>
    <link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet">
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
            <?php wp_nav_menu(array('theme_location' => 'primary-menu')); ?>
            <br class="clear">
          </nav>
          <div class="tagline">
            <?php bloginfo('description'); ?>
          </div>
          <br class="clear">
        </div>
      </div>
    </header>
    <div class="container">
