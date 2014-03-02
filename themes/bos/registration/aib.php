<?php

if (!class_exists('AIB_Custom_Post')) {

  class AIB_Custom_Post extends Custom_Post_Type {

    var $version = 0.3;
    var $post_type = 'aib';
    var $editable = true;

    function __construct() {
      $this->dir = get_template_directory_uri() . '/registration';
      $this->path = get_template_directory() . '/registration';
      $this->setup_post(array(
        'label' => __('Listings'),
        'singular_label' => __('Listing'),
        'supports' => array(
          'post-thumbnails'
        ),
        'exclude_from_search' => false
      ));

      $this->setup_taxonomy('locations', array(
        'label' => __('Locations')
      ));
      $this->setup_taxonomy('media', array(
        'label' => __('Media Types')
      ));
      $this->setup_taxonomy('attributes', array(
        'label' => __('Attributes')
      ));

      add_filter('get_terms', array(&$this, 'cleanup_term_names'));
      add_action('admin_print_styles-media-upload-popup', array(&$this, 'hide_image_controls'));

    }

    function edit_post($post) {
      wp_enqueue_style($this->post_type, "$this->dir/$this->post_type.css", array(), $this->version, 'all');
      wp_enqueue_script($this->post_type, "$this->dir/$this->post_type.js", array('jquery'), $this->version, true);
      remove_meta_box('locationsdiv', $this->post_type, 'side');

      $type = $post->post_type;

      $text_fields = array(
        'organization',
        'url',
        'time_friday',
        'time_saturday',
        'time_sunday',
        'admission_price',
        'location_room'
      );
      foreach ($text_fields as $field) {
        $post->$field = esc_attr($this->get($post->ID, $field));
      }

      $checkbox_fields = array(
        'select_friday',
        'select_saturday',
        'select_sunday',
        'admission_free'
      );
      foreach ($checkbox_fields as $field) {
        $value = $this->get($post->ID, $field);
        if (!empty($value)) {
          $post->$field = ' checked="checked"';
        }
      }

      $listing_type = $this->get($post->ID, 'listing_type');
      if ($listing_type == 'open_studio') {
        $post->type_open_studio = ' checked="checked"';
      } else if ($listing_type == 'event') {
        $post->type_event = ' checked="checked"';
      }

      $post->url = esc_attr($this->get($post->ID, 'url'));

      $locations = get_terms("locations", 'hide_empty=0');
      $locations_selected = wp_get_object_terms($post->ID, "locations");
      foreach ($locations as $location) {
        $selected = in_array($location, $locations_selected) ? ' selected="selected"' : '';
        $location_options .= "<option$selected>$location->name</option>\n";
      }
      $post->location_options = $location_options;

      if (!empty($locations_selected)) {
        $post->locations_existing = ' checked="checked"';
      }

      $sections = array(
        'listing' => __('General Info'),
        'details' => __('Open Studios or Event Information'),
        'images' => __('Photos'),
        'location' => __('Location'),
        'description' => __('Artist Statement or Event Description')
      );
      foreach ($sections as $id => $label) {
        $template = "$this->path/templates/$id.html";
        $callback = create_function('$object', "render_template('$template', \$object);");
        add_meta_box($id, $label, $callback, $type, 'normal', 'high');
      }
    }

    function save_post($id) {
      $fields = array(
        'organization',
        'url',
        'listing_type',
        'select_friday',
        'select_saturday',
        'select_sunday',
        'time_friday',
        'time_saturday',
        'time_sunday',
        'admission_free',
        'admission_price',
        'location_room'
      );
      foreach ($fields as $key) {
        $value = '';
        if (!empty($_POST[$key])) {
          $value = $_POST[$key];
        }
        $this->set($id, $key, $value);
      }
      if (!empty($_POST['location_source'])) {
        if ($_POST['location_source'] == 'existing') {
          wp_set_object_terms($id, $_POST['location'], 'locations');
        } else if ($_POST['location_source'] == 'create') {
          wp_insert_term($_POST['location_name'], 'locations', array(
            'description' => $_POST['location_address']
          ));
          wp_set_object_terms($id, $_POST['location_name'], 'locations');
        }
      }
    }

    function cleanup_term_names($terms) {
      if (is_array($terms)) {
        array_walk($terms, array(&$this, 'cleanup_term_name'));
      }
      return $terms;
    }

    function cleanup_term_name($term) {
      if (is_object($term) && $term->taxonomy == 'media' || $term->taxonomy == 'attributes') {
        $term->name = preg_replace('/^\d+\s+(.+)/', '$1', $term->name);
      }
    }

    function hide_image_controls() {
      return;
      echo <<<END
<style type="text/css">
.slidetoggle tbody {
  display: none;
}
.media-item-info .button {
  display: none;
}
</style>
END;
    }

  }

}

?>
