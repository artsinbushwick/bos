<?php

require __DIR__ . '/lib/theme.php';
require __DIR__ . '/registration/registration.php';

class BOS_Theme extends Theme {
  
  function __construct() {
    $this->add_action('init');
    $this->add_action('widgets_init');
    $this->add_action('customize_register');
    $this->registration = new AIB_Registration();
  }
  
  function init() {
    register_nav_menus(array(
      'primary-menu' => 'Below logo',
      'secondary-menu' => 'Above logo',
      'footer-menu' => 'Bottom of page'
    ));
  }
  
  function widgets_init() {
    register_sidebar( array(
      'name' => 'Sidebar',
      'id' => 'sidebar',
      'before_widget' => '<div class="widget">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>',
    ));
  }
  
  function customize_register($wp_customize) {
    $wp_customize->add_section('bos', array(
      'title'      => __('Bushwick Open Studios', 'bos'),
      'priority'   => 30
    ));
    $days = array(
      'friday' => 'May 30',
      'saturday' => 'May 31',
      'sunday' => 'June 1'
    );
    foreach ($days as $day => $date) {
      $id = "bos_{$day}_date";
      $wp_customize->add_setting($id, array(
        'default'     => $date,
        'transport'   => 'refresh'
      ));
      $wp_customize->add_control(new WP_Customize_Control($wp_customize, $id, array(
        'label'      => ucwords("$day Date"),
        'section'    => 'bos',
        'settings'   => $id
      )));
    }
  }
  
  function page_title() {
    $title = wp_title('|', false, 'right');
    $title .= get_bloginfo('name');
    $description = get_bloginfo('description', 'display');
    if (!empty($description) && (is_home() || is_front_page())) {
      $title .= " | $description";
    }
    return strip_tags($title);
  }
  
  function header_count() {
    $dir = get_template_directory() . '/headers';
    $headers = glob("$dir/header*.jpg");
    return count($headers);
  }
  
}
$bos = new BOS_Theme();

// This walker class is used for the footer links, to split them into 3 columns
class BOS_Walker_Nav_Menu extends Walker_Nav_Menu {
  
  function start_el(&$output, $item, $depth, $args) {
    global $wp_query;
    if (empty($this->index)) {
      $this->index = 0;
    }
    $indent = ($depth > 0 ? str_repeat( "  ", $depth) : ''); // code indent
    $output .= "$indent<li>";
    $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
    $before = $args->before;
    if (!empty($args->columns)) {
      $cols = $args->columns;
      $count = $args->count;
      $per_col = ceil($count / $cols);
      if ($this->index % $per_col == 0) {
        $before .= '</ul><ul>';
      }
    }
    $item_output = sprintf('%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
      $before,
      $attributes,
      $args->link_before,
      apply_filters('the_title', $item->title, $item->ID),
      $args->link_after,
      $args->after
    );
  
    // build html
    $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    $this->index++;
  }
  
}
