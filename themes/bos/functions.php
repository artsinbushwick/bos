<?php

require __DIR__ . '/lib/theme.php';
require __DIR__ . '/registration/registration.php';

if (function_exists('add_image_size')) { 
	add_image_size('bio-thumb', 190, 100, true);
}

if (function_exists('add_theme_support')) { 
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
}

class BOS_Theme extends Theme {
  
  /*
  
  These terms get created when the theme is activated. Since they are sorted by
  ID, you will need to delete all of them to insert one in the middle of a list.
  To regenerate the list, just deactivate/reactivate the theme.
  
  */
  var $media_terms = array(
    'Visual Arts' => array(
      'Painting',
      'Photography',
      'Drawing',
      'Sculpture',
      'Printmaking',
      'Installation',
      'Book Arts',
      'Street Art',
      'Mixed Media'
    ),
    'Performing Arts' => array(
      'Music',
      'Theater',
      'Dance',
      'Performance Art',
      'Comedy'
    ),
    'Design/Media/Craft' => array(
      'Industrial Design',
      'Film',
      'Video',
      'Graphic Design',
      'Fashion/Clothing/Jewelry',
      'Craft/Folk Arts',
      'Technology/Electronics/Computers',
      'Food/Culinary Arts',
      'Architecture'
    ),
    'Miscellaneous' => array(
      'Writing/Literary Arts',
      'Discussion/Panel',
      'Workshop',
      'Walking Tours',
      'Environmental/Activist Art'
    ),
    'Other' => array()
  );
  
  var $attribute_terms = array(
    'Is Child-Friendly',
    'Is Participatory/Interactive',
    'Will Take Place Outdoors',
    'Will Have Free Food',
    'Will Have Food For Sale',
    'Will Have Free Drinks',
    'Will Have Drinks For Sale',
    'Will Include Work For Sale',
    'Hablamos Español',
    'Handicapped-accessible'
  );
  
  function __construct() {
    $this->add_action('init');
    $this->add_action('after_switch_theme');
    $this->add_action('widgets_init');
    $this->add_action('customize_register');
    $this->add_action('get_terms_orderby');
    $this->add_action('wp_before_admin_bar_render');
    $this->add_filter('custom_menu_order');
    $this->add_action('admin_menu');
    $this->add_filter('menu_order', 'custom_menu_order');
    $this->registration = new AIB_Registration();
  }
  
  function init() {
    register_nav_menus(array(
      'primary-menu' => 'Below logo',
      'secondary-menu' => 'Above logo',
      'footer-menu' => 'Bottom of page'
    ));
    register_post_type('bio', array(
      'labels' => array(
        'name' => __('Team Bios'),
        'singular_name' => __('Bio')
      ),
      'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
      'menu_position' => 5,
      'public' => true,
      'has_archive' => true,
      'taxonomies' => array()
    ));
    register_post_type('sponsor', array(
      'labels' => array(
        'name' => __('Sponsors'),
        'singular_name' => __('Sponsor')
      ),
      'supports' => array('title', 'editor', 'page-attributes'),
      'menu_position' => 6,
      'public' => false,
      'show_ui' => true,
      'has_archive' => false,
      'taxonomies' => array()
    ));
  }
  
  function after_switch_theme() {
    foreach ($this->media_terms as $category => $terms) {
      $cat_term = term_exists($category, 'media', 0);
      if (!$cat_term) {
        $cat_term = wp_insert_term($category, 'media');
      }
      foreach ($terms as $term) {
        if (!term_exists($term, 'media', $cat_term['term_id'])) {
          wp_insert_term($term, 'media', array(
            'parent' => $cat_term['term_id']
          ));
        }
      }
    }
    foreach ($this->attribute_terms as $term) {
      if (!term_exists($term, 'attributes', 0)) {
        wp_insert_term($term, 'attributes');
      }
    }
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
  
  function wp_before_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('post');
    $wp_admin_bar->remove_menu('wp-logo');
  }
  
  function admin_menu() {
    remove_menu_page('edit.php');           // Posts
    remove_menu_page('edit-comments.php');  // Comments
  }
  
  function custom_menu_order($menu_ord) {
    if (!$menu_ord) {
      return true;
    }
    return array(
      'index.php',                      // Dashboard
      'edit.php?post_type=page',        // Pages
      'edit.php?post_type=sponsor',     // Sponsors
      'edit.php?post_type=bio',         // Team Bios
      'edit.php?post_type=aib',         // Listings
      'separator1',                     // First separator
      'upload.php',                     // Media
      'themes.php',                     // Appearance
      'plugins.php',                    // Plugins
      'tools.php',                      // Tools
      'users.php',                      // Users
    );
  }
  
  function get_terms_orderby() {
    return 't.term_id';
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
