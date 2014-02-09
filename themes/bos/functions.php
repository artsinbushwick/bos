<?php

function bos_nav_menus() {
  register_nav_menus(array(
    'main-menu' => 'Below logo',
    'secondary-menu' => 'Above logo',
    'footer-menu' => 'Bottom of page'
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
