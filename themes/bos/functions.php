<?php

require_once __DIR__ . '/lib/theme.php';
require_once __DIR__ . '/registration/registration.php';
require_once __DIR__ . '/seeking/seeking.php';

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
    $this->add_action('wp_ajax_bos_directory_locations', 'directory_locations');
    $this->add_action('wp_ajax_nopriv_bos_directory_locations', 'directory_locations');
    $this->add_action('wp_ajax_bos_directory_search', 'directory_search');
    $this->add_action('wp_ajax_nopriv_bos_directory_search', 'directory_search');
    $this->add_action('wp_ajax_bos_update_post_data', 'update_post_data');
    $this->add_action('wp_ajax_nopriv_bos_update_post_data', 'update_post_data');
    $this->add_action('wp_ajax_bos_lookup', 'lookup');
    $this->add_action('wp_ajax_bos_geocode', 'geocode');
    //$this->add_filter('post_type_link', 'post_type_link', 10, 3);
    $this->setup_rewrites();
    $this->registration = new AIB_Registration();
		$this->seeking = new AIB_Seeking();
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
    remove_filter('the_content', 'wptexturize');
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
  
  function directory_locations($hubs = false) {
    global $wpdb, $blog_id, $table_prefix;
    
    if (!empty($_GET['hubs'])) {
      $hubs = true;
    }
    
    if ($_SERVER['HTTP_HOST'] == 'dev.artsinbushwick.org') {
      $blog_id = 10;
    }
    
    if (!empty($hubs)) {
      $locations = $wpdb->get_results("
        SELECT id, BOS14number AS marker, lat, lng, address
        FROM aib_location
        WHERE BOS14number REGEXP '[A-Z]'
        ORDER BY BOS14number
      ");
    } else {
      $locations = $wpdb->get_results("
        SELECT id, BOS14number AS marker, lat, lng, address
        FROM aib_location
        WHERE BOS14number REGEXP '[0-9]+'
        ORDER BY CAST(BOS14number AS UNSIGNED)
      ");
    }
    $count = count($locations);
    $ids = array();
    $location_lookup = array();
    foreach ($locations as $l) {
      $l->listings = array();
      $ids[] = intval($l->id);
      $location_lookup[$l->id] = $l;
    }
    $id_list = implode(', ', $ids);
    $listings = $wpdb->get_results("
      SELECT post_id, location_id, main_image,
             artists, organization, event_name, primary_name, short_description,
             space_name, street_address, room_number, zip_code
      FROM aib_listing, $wpdb->posts
      WHERE site_id = $blog_id
        AND location_id IN ($id_list)
        AND aib_listing.post_id = $wpdb->posts.ID
        AND post_status = 'publish'
      ORDER BY order_in_building, post_id
    ");
    $ids = array();
    $listing_lookup = array();
    $image_parent_ids = array();
    $parent_clause = '';
    foreach ($listings as $listing) {
      $ids[] = $listing->post_id;
      $listing_lookup[$listing->post_id] = $listing;
      if ($listing->main_image) {
        list($image, $width, $height) = wp_get_attachment_image_src($listing->main_image, 'medium');
        $listing->image = $image;
      } else {
        $image_parent_ids[] = $listing->post_id;
      }
      $location = $location_lookup[$listing->location_id];
      foreach (get_object_vars($listing) as $key => $value) {
        if (!is_numeric($value)) {
          $listing->$key = htmlentities(stripslashes($value), ENT_COMPAT, 'UTF-8');
        }
      }
      $location->listings[] = $listing;
    }
    if (!empty($image_parent_ids)) {
      $parent_ids = implode(', ', $image_parent_ids);
      $parent_clause = "OR post_parent IN ($parent_ids)";
    }
    $id_list = implode(', ', $ids);
    $posts = $wpdb->get_results("
      SELECT ID, post_parent, post_name
      FROM {$table_prefix}posts
      WHERE ID IN ($id_list)
      $parent_clause
    ");
    $base = get_bloginfo('url');
    if (!empty($posts)) {
      foreach ($posts as $post) {
        if (!empty($post->post_parent)) {
          $listing = $listing_lookup[$post->post_parent];
          if (empty($listing->image)) {
            list($image, $width, $height) = wp_get_attachment_image_src($post->ID, 'medium');
            $listing->image = $image;
          }
        } else if (!empty($post->post_name)) {
          $listing = $listing_lookup[$post->ID];
          $listing->url = "$base/directory/{$post->post_name}/";
        }
      }
    }
    $args = array(
      'fields' => 'all_with_object_id'
    );
    $terms = wp_get_object_terms($ids, array('media', 'attributes'), $args);
    foreach ($terms as $term) {
      $listing = $listing_lookup[$term->object_id];
      if ($term->taxonomy == 'attributes') {
        $term_class = "attrs-$term->slug";
      } else {
        $term_class = "media-$term->slug";
      }
      if (empty($listing->filter_terms)) {
        $listing->filter_terms = array($term_class);
      } else {
        array_push($listing->filter_terms, $term_class);
      }
    }
    if (strpos($_SERVER['PHP_SELF'], 'admin-ajax.php') !== false &&
        !empty($_GET['action']) &&
        $_GET['action'] == 'bos_directory_locations') {
      header('Content-Type: application/json');
      echo json_encode($locations);
      exit;
    } else {
      return $locations;
    }
  }
  
  function pre_get_posts($query) {
    if (preg_match('#directory/(.+?)/?$#', $_SERVER['REQUEST_URI'], $matches)) {
      $query->query_vars['post_type'] = array('nav_menu_item', 'aib', 'page');
      $query->query_vars['aib'] = $matches[1];
      $query->query_vars['name'] = '';
      $query->query_vars['pagename'] = '';
    }
    return $query;
  }
  
  function setup_rewrites() {
    global $wp_rewrite;
    $gallery_structure = '/directory/%aib%';
    $wp_rewrite->add_rewrite_tag("%aib%", '([^/]+)', "aib=");
    $wp_rewrite->add_permastruct('aib', $gallery_structure, false);
  }
  
  function directory_search() {
    $query = $_GET['query'];
    $posts = get_posts(array(
      'post_type' => 'aib',
      's' => $query,
      'posts_per_page' => -1
    ));
    $ids = array();
    foreach ($posts as $post) {
      $ids[] = $post->ID;
    }
    header('Content-Type: application/json');
    echo json_encode(array(
      'query' => htmlentities(stripslashes($query), ENT_COMPAT, 'UTF-8'),
      'ids' => $ids
    ));
    exit;
  }
  
  function update_post_data() {
    global $blog_id, $wpdb;
    set_time_limit(0);
    echo "<pre>";
    if (!empty($_GET['id']) && $_GET['id'] == 'all') {
      $ids = $wpdb->get_col("
        SELECT ID
        FROM $wpdb->posts
        WHERE post_type = 'aib'
          AND post_status = 'publish'
        ORDER BY ID
      ");
    } else if (!empty($_GET['id'])) {
      $ids = intval($_GET['id']);
      $ids = array($ids);
      if (!current_user_can('edit_post', $ids)) {
        exit;
      }
    }
    $count = count($ids);
    echo "Found $count listings\n";
    if (!empty($ids)) {
      $pos = (empty($_GET['pos'])) ? 0 : intval($_GET['pos']);
      $ids_page = array_slice($ids, $pos, 50);
      $ids_list = implode(', ', $ids_page);
      $listings = $wpdb->get_results("
        SELECT BOS14number, post_id, building_number, building_name, artists, organization, event_name, website, space_name, street_address, room_number, zip_code, short_description, media_other
        FROM aib_listing, aib_location
        WHERE post_id IN ($ids_list)
          AND aib_listing.location_id = aib_location.id
      ");
      foreach ($listings as $listing) {
        $post = get_post($listing->post_id);
        echo "$post->post_title ($post->ID)\n";
        $content = preg_replace('#<\!--\[meta\].+\[/meta\]-->#ms', '', $post->post_content);
        $vars = get_object_vars($listing);
        $vars['BOS14number'] = "bos:{$vars['BOS14number']}";
        $values = array_values($vars);
        $values = implode(" ", $values);
        $values .= aib_get_show_media($post);
        $values .= aib_get_show_features($post);
        $content = "$content\n<!--[meta]{$values}[/meta]-->";
        wp_update_post(array(
          'ID' => $listing->post_id,
          'post_content' => $content
        ));
      }
      if (count($ids_page) == 50) {
        $pos += 50;
        $url = get_bloginfo('url');
        $next_page = "$url{$_SERVER['PHP_SELF']}?action=bos_update_post_data&id=all&pos=$pos";
      }
    }
    if (!empty($next_page)) {
      echo "<script>window.location = '$next_page';</script>";
    } else {
      echo "Updating directory locations\n";
      $hubs = $this->directory_locations(true);
      echo "Updating directory hubs\n";
      $locations = $this->directory_locations(false);
      update_option('directory_hubs', $hubs);
      update_option('directory_locations', $locations);
      echo "Done\n";
    }
    exit;
  }
  
  function geocode() {
    global $wpdb;
    set_time_limit(0);
    $listings = $wpdb->get_results("
      SELECT id, address, zip
      FROM aib_location
      WHERE address IS NOT NULL
        AND address <> ''
        AND zip IS NOT NULL
        AND zip <> ''
    ");
    echo '<pre>';
    foreach ($listings as $listing) {
      echo "Geocoding $listing->address $listing->zip ($listing->id)\n";
      $address = rawurlencode($listing->address);
      $zip = rawurlencode($listing->zip);
      // this key is obsolete
      $api_key = 'AIzaSyCO6b9TF6Ts7VFIKwm22eOayIO90SsMtjM';
      $url = "https://maps.googleapis.com/maps/api/geocode/json" .
             "?address=$address,%20Brooklyn,%20NY%20$zip" .
             "&sensor=false&key=$api_key";
      $response = wp_remote_get($url);
      $response = json_decode($response['body']);
      if (!empty($response->results)) {
        $result = array_shift($response->results);
        $lat = $result->geometry->location->lat;
        $lng = $result->geometry->location->lng;
        echo "  ($lat, $lng)\n";
        $wpdb->query($wpdb->prepare("
          UPDATE aib_location
          SET lat = %s, lng = %s
          WHERE id = %d
        ", $lat, $lng, $listing->id));
      } else {
        echo "  <b>Unable to geocode</b>\n";
      }
    }
    exit;
  }
  
  function lookup() {
    echo <<<END
        <form action="admin-ajax.php">
          <input type="hidden" name="action" value="bos_lookup">
          Email:
          <input type="email" name="email">
          <input type="submit" value="Lookup">
        </form>
END;
    if (!empty($_GET['email'])) {
      global $wpdb;
      $post_id = $wpdb->get_var($wpdb->prepare("
        SELECT meta_value
        FROM $wpdb->usermeta,
             $wpdb->users
        WHERE meta_key = 'aib_post_bos2014'
          AND user_email = %s
          AND user_id = ID
      ", $_GET['email']));
      if (!empty($post_id)) {
        echo $post_id;
        $post = get_post($post_id);
        if ($post->post_status != 'publish') {
          echo " [unpublished]";
        }
        if (empty($post->post_name)) {
          echo " [empty post_name]";
        }
        $listing = $wpdb->get_row($wpdb->prepare("
          SELECT *
          FROM aib_listing
          WHERE post_id = %s
        ", $post_id));
        if (empty($listing->location_id)) {
          echo " [empty location_id]";
        }
        if (!empty($listing->street_address)) {
          echo " [$listing->street_address]";
        }
        $url = get_permalink($post_id);
        echo " <a href=\"post.php?post=$post_id&action=edit\">edit</a>";
        echo " <a href=\"$url\">view</a>";
        echo " <a href=\"?action=bos_update_post_data&id=$post_id\">update</a>";
      }
    }
    exit;
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

function aib_dates() {
  $friday = get_theme_mod('bos_friday_date');
  $saturday = get_theme_mod('bos_saturday_date');
  $sunday = get_theme_mod('bos_sunday_date');
  return array(
    'friday' => $friday,
    'saturday' => $saturday,
    'sunday' => $sunday
  );
}

function aib_primary_name($post) {
  $key = $post->primary_name;
  if (!empty($key) && !empty($post->$key)) {
    echo esc_html($post->$key);
  } else {
    global $wpdb;
    if (!empty($post->artists)) {
      $post->primary_name = 'artists';
      $wpdb->update('aib_listing', array(
        'primary_name' => 'artists'
      ), array('post_id' => $post->ID));
      echo $post->artists;
    } else if (!empty($post->organization)) {
      $post->primary_name = 'organization';
      $wpdb->update('aib_listing', array(
        'primary_name' => 'organization'
      ), array('post_id' => $post->ID));
      echo $post->organization;
    } else if (!empty($post->event_name)) {
      $post->primary_name = 'event_name';
      $wpdb->update('aib_listing', array(
        'primary_name' => 'event_name'
      ), array('post_id' => $post->ID));
      echo $post->event_name;
    } else {
      echo 'Untitled Show';
    }
  }
}

function aib_subtitle($post) {
  echo aib_get_subtitle($post);
}

function aib_get_subtitle($post) {
  $key = $post->primary_name;
  $subtitle = array();
  if ($key != 'artists' && !empty($post->artists)) {
    $subtitle[] = esc_html($post->artists);
  }
  if ($key != 'organization' && !empty($post->organization)) {
    $subtitle[] = esc_html($post->organization);
  }
  if ($key != 'event_name' && !empty($post->event_name)) {
    $subtitle[] = esc_html($post->event_name);
  }
  return implode(', ', $subtitle);
}

function aib_content($post) {
  echo aib_get_content($post);
}

function aib_get_content($post) {
  $content = $post->post_content;
  $content = preg_replace('#<\!--\[meta\].+\[/meta\]-->#ms', '', $content);
  $content = preg_replace('#&lt;\!--\[meta\].+\[/meta\]--&gt;#ms', '', $content);
  $content = apply_filters('the_content', $content);
  if (empty($content) && !empty($post->post_excerpt)) {
    $content = $post->post_excerpt;
  }
  return $content;
}

function aib_excerpt($post) {
  $excerpt = $post->post_excerpt;
  /*if (strlen($excerpt) > 100) {
    $excerpt = substr($excerpt, 0, 97) . '...';
  }*/
  echo esc_html($excerpt);
}

function aib_thumbnail($post, $just_image = false, $delay_load = false) {
  if (empty($post->main_image)) {
    global $wpdb;
    $images = get_children(array(
      'post_parent' => $post->ID,
      'post_type' => 'attachment',
      'post_mime_type' => 'image'
    ));
    if (!empty($images)) {
      foreach ($images as $id => $image) {
        $post->main_image = $id;
        break;
      }
      $wpdb->update('aib_listing', array(
        'main_image' => $post->main_image
      ), array('post_id' => $post->ID));
    }
  }
  $image = wp_get_attachment_image($post->main_image);
  if ($just_image) {
    echo $image;
  } else {
    if (!empty($delay_load)) {
      list($src, $width, $height) = wp_get_attachment_image_src($post->main_image);
      echo "<a href=\"?listing=$post->ID\"><img src=\"data:null\" data-src=\"$src\" width=\"$width\" height=\"$height\" class=\"pending\" /></a>";
    } else {
      echo "<a href=\"?listing=$post->ID\">$image</a>";
    }
  }
}

function aib_images($post) {
  $images = get_children(array(
    'post_parent' => $post->ID,
    'post_type' => 'attachment',
    'post_mime_type' => 'image'
  ));
  $main_image = $post->main_image;
  if (count($images) > 1) {
    echo '<div class="thumbs">';
    foreach ($images as $id => $attachment) {
      $selected = '';
      if ($id == $main_image) {
        $selected = ' selected';
      }
      list($url) = wp_get_attachment_image_src($id, 'large');
      $img = wp_get_attachment_image($id);
      echo "<a href=\"$url\" class=\"thumb$selected\">$img</a>";
    }
    echo '<br class="clear">';
    echo '</div>';
  }
  if (!empty($main_image) || !empty($images)) {
    if (empty($main_image) && !empty($images)) {
      foreach ($images as $id => $image) {
        $main_image = $id;
        break;
      }
    }
    list($url) = wp_get_attachment_image_src($main_image, 'large');
    $img = wp_get_attachment_image($main_image, 'large');
    echo "<a href=\"$url\" class=\"feature\">$img</a>";
  }
}

function aib_show_times($post) {
  if (!empty($post->select_friday)) {
    echo aib_format_times($post, 'friday');
    echo "<br />";
  }
  if (!empty($post->select_saturday)) {
    echo aib_format_times($post, 'saturday');
    echo "<br />";
  }
  if (!empty($post->select_sunday)) {
    echo aib_format_times($post, 'sunday');
    echo "<br />";
  }
}

function aib_show_location($post) {
  echo aib_get_show_location($post);
}

function aib_number_of_participants($post) {
  if (!is_numeric($post->artist_count)) {
    return '';
  }
  $s = ($post->artist_count > 1) ? 's' : '';
  echo "$post->artist_count participant$s";
}

function aib_get_show_location($post) {
  global $locations;
  if (empty($locations)) {
    aib_load_locations();
  }
  if (empty($post->location_id)) {
    return '<i>No location assigned</i>';
  } else {
    $location = $locations[$post->location_id];
    $title = aib_get_location_title($post);
    if (!empty($title)) {
      $title = "<strong>$title</strong><br />\n";
    }
    $room = '';
    if (!empty($post->room_number)) {
      $room = ", $post->room_number";
    }
    $hub = '';
    if (!is_numeric($location->BOS14number)) {
      $hub = ' hub';
    }
    $marker = "<div class=\"marker$hub\">$location->BOS14number</div>";
    return "$marker$title$location->address$room<br />Brooklyn, NY $location->zip";
  }
}

function aib_show_media($post) {
  echo aib_get_show_media($post);
}

function aib_get_show_media($post) {
  $terms = wp_get_object_terms($post->ID, 'media');
  $media = array();
  foreach ($terms as $term) {
    $name = $term->name;
    $name = preg_replace('/\d+\s(.+)/', '$1', $name);
    if ($name == 'Other' && !empty($post->media_other)) {
      $name = esc_html($post->media_other);
    }
    $media[] = $name;
  }
  return implode(", ", $media);
}

function aib_show_features($post) {
  echo aib_get_show_features($post);
}

function aib_get_show_features($post) {
  $terms = wp_get_object_terms($post->ID, 'attributes');
  $attributes = array();
  foreach ($terms as $term) {
    $name = $term->name;
    $name = preg_replace('/\d+\s(.+)/', '$1', $name);
    $attributes[] = $name;
  }
  if (empty($terms)) {
    return "";
  } else {
    return implode("<br />\n", $attributes);
  }
}

function aib_show_links($post) {
  echo aib_get_show_links($post);
}

function aib_get_show_links($post) {
  global $wpdb;
  $result = '';
  if (!empty($post->website)) {
    $urls = explode(',', $post->website);
    foreach ($urls as $url) {
      $url = trim($url);
      if (substr($url, 0, 4) != 'http') {
        $url = "http://$url";
      }
      $show_url = preg_replace('#^http://#', '', $url);
      $show_url = preg_replace('#(.+)/#', '$1', $show_url);
      $result .= "&raquo; <a href=\"$url\" target=\"_new\">$show_url</a><br />\n";
    }
  }
  if (!empty($post->contact_email)) {
    $result .= "&raquo; <a href=\"mailto:$post->contact_email\">Contact</a><br />";
  } else {
    $email = $wpdb->get_var("
      SELECT user_email
      FROM $wpdb->users
      WHERE ID = $post->post_author
    ");
    $result .= "&raquo; <a href=\"mailto:$email\">Contact</a><br />";
  }
  return $result;
}

function aib_show_map($post) {
  global $wpdb;
  $location = $wpdb->get_row("
    SELECT lat, lng
    FROM aib_location
    WHERE id = $post->location_id
  ");
  $icon = 'http://artsinbushwick.org/bos2013/wp-content/themes/bos2013/images/markers/22.png';
  $center = urlencode("$location->lat,$location->lng");
  $url = "http://maps.google.com/maps/api/staticmap?center=$center&zoom=15&size=125x150&sensor=false&markers=icon:$icon|$center";
  $alt = esc_attr($post->address);
  echo "<img src=\"$url\" alt=\"$alt\" width=\"125\" height=\"150\" class=\"map\" />";
}

function aib_format_times($post, $day) {
  global $blog_id;
  $aib_dates = aib_dates();
  extract($aib_dates);
  $formatted = '';
  $time_var = "time_$day";
  $additional_time_var = "additional_time_$day";
  if ($day == 'friday') {
    $formatted .= "Friday $friday";
  } else if ($day == 'saturday') {
    $formatted .= "Saturday $saturday";
  } else if ($day == 'sunday') {
    $formatted .= "Sunday $sunday";
  }
  if (!empty($post->$time_var)) {
    $time = esc_html($post->$time_var);
    $formatted .= ", $time";
  }
  if (!empty($post->$additional_time_var)) {
    $additional = esc_html($post->$additional_time_var);
    $formatted .= " (event at $additional)";
  }
  return $formatted;
}

function aib_load_locations() {
  global $wpdb, $locations;
  $objects = $wpdb->get_results("
    SELECT *
    FROM aib_location
  ");
  $locations = array();
  foreach ($objects as $object) {
    $locations[$object->id] = $object;
  }
}

function aib_pagination($posts, $perpage) {
  if (count($posts) == 0) {
    return;
  }
  $pages = ceil(count($posts) / $perpage);
  echo '<div class="pagination">';
  echo "<div class=\"left\">";
  echo '<strong><a href="#results" class="disabled prev">&laquo; Prev</a></strong>';
  echo " // ";
  $disabled = ($pages == 1) ? 'disabled' : '';
  echo "<strong><a href=\"#results\" class=\"$disabled next\">Next &raquo;</a></strong>";
  echo " &nbsp; /// &nbsp; ";
  echo 'Page <select name="pg" id="page-select">';
  for ($i = 1; $i <= $pages; $i++) {
    echo "<option>$i</option>\n";
  }
  echo "</select> of $pages";
  echo '</div>';
  echo "<div class=\"right\">";
  echo "Shows per page: ";
  echo '<select name="perpage" id="perpage">';
  
  $perpage_options = array(10, 25, 50, 'All');
  foreach ($perpage_options as $option) {
    $selected = (@$_GET['perpage'] == $option) ? ' selected="selected"' : '';
    echo "<option$selected>$option</option>\n";
  }
  
  echo '</select>';
  echo '</div>';
  echo '<br class="clear" />';
  echo '</div>';
  
}

function aib_day_filter() {
  global $blog_id;
  $aib_dates = aib_dates();
  extract($aib_dates);
  $fr = ($_GET['day'] == 'fr') ? ' selected="selected"' : '';
  $sa = ($_GET['day'] == 'sa') ? ' selected="selected"' : '';
  $su = ($_GET['day'] == 'su') ? ' selected="selected"' : '';
  echo "<select name=\"day\">
  <option value=\"\">All days</option>
  <option value=\"fr\"$fr>Friday $friday</option>
  <option value=\"sa\"$sa>Saturday $saturday</option>
  <option value=\"su\"$su>Sunday $sunday</option>
  </select>";
}

function aib_media_filter() {
  $terms = get_terms('media');
  //$sort = create_function('$a, $b', 'if ($a->name == $b->name) return 0; if ($a->name == "Other") return 1; if ($b->name == "Other") return -1; return ($a->name < $b->name) ? -1 : 1;');
  //usort($terms, $sort);
  echo "<select name=\"media\">";
  echo "<option value=\"\">All media</option>\n";
  foreach ($terms as $term) {
    $selected = (@$_GET['media'] == $term->term_id) ? ' selected="selected"' : '';
    echo "<option value=\"$term->term_id\"$selected>$term->name</option>\n";
  }
  echo "</select>";
}

function aib_feature_filter() {
  $terms = get_terms('attributes');
  foreach ($terms as $n => $term) {
    if ($n == ceil(count($terms) / 2)) {
      echo '</div><div class="column">';
    }
    $selected = (!empty($_GET['fe'][$term->term_id])) ? ' checked="checked"' : '';
    echo "<input type=\"checkbox\" name=\"fe[$term->term_id]\" value=\"1\" $selected/> $term->name<br />\n";
  }
}

function aib_search() {
  $q = esc_attr(@$_GET['q']);
  echo '<label>Search listings';
  echo "<input type=\"text\" name=\"q\" value=\"$q\" id=\"search\" />";
  echo '</label>';
}

function aib_latlng($post) {
  global $locations;
  if (empty($locations)) {
    aib_load_locations();
  }
  if (!empty($post->location_id)) {
    $location = $locations[$post->location_id];
    echo "$location->lat, $location->lng";
  }
}

function aib_address($post) {
  global $locations;
  if (empty($locations)) {
    aib_load_locations();
  }
  if (!empty($post->location_id)) {
    $location = $locations[$post->location_id];
    /*$title = aib_get_location_title($post);
    if (!empty($title)) {
      echo esc_attr("$title - ");
    }*/
    echo esc_attr($location->address);
  }
}

function aib_admission($post) {
  if (!empty($post->admission_price)) {
    echo "<h5>Admission</h5>\n";
    echo esc_html($post->admission_price);
  }
}

function aib_location_id($post) {
  global $locations;
  if (empty($locations)) {
    aib_load_locations();
  }
  if (!empty($post->location_id)) {
    $location = $locations[$post->location_id];
    //echo esc_attr($location->number);
    echo esc_attr($location->BOS13number);
  }
}

function aib_marker($post) {
  $url = aib_get_marker_url($post);
  echo "<img src=\"$url\" class=\"marker\" alt=\"\" />";
}

function aib_get_marker_url($post) {
  global $locations;
  $dir = dirname(get_bloginfo('stylesheet_url'));
  if (empty($locations)) {
    aib_load_locations();
  }
  if (!empty($post->location_id)) {
    $location = $locations[$post->location_id];
    //if (is_numeric($location->number)) {
    if (is_numeric($location->BOS13number)) {
      return "$dir/images/markers/40-$location->BOS13number.png";
      //return "$dir/images/markers/40-$location->number.png";
    } else {
      //$number = strtolower($location->number);
      $number = strtolower($location->BOS13number);
      return "$dir/images/markers/hub/40-$number.png";
    }
  } else {
    return "$dir/images/markers/hub/40.png";
  }
}

function aib_get_location_title($post) {
  global $locations;
  if (empty($locations)) {
    aib_load_locations();
  }
  $location = $locations[$post->location_id];
  if (!empty($post->space_name)) {
    return $post->space_name;
  } else if (!empty($location->title)) {
    return $location->title;
  } else {
    return '';
  }
}

?>
