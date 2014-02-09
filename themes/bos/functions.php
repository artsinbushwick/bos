<?php

function bos_nav_menus() {
  register_nav_menus(array(
    'main-menu' => 'Below logo',
    'secondary-menu' => 'Above logo'
  ));
}
add_action('init', 'bos_nav_menus');

function bos_title() {
  $title = wp_title('|', false, 'right');
  $title .= get_bloginfo('name');
  $description = get_bloginfo('description', 'display');
  if (!empty($description) && (is_home() || is_front_page())) {
    $title .= " | $description";
  }
  return $title;
}

function bos_header_count() {
  $dir = get_template_directory() . '/headers';
  $headers = glob("$dir/header*.jpg");
  return count($headers);
}
