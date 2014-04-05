<?php

if (!class_exists('Custom_Post_Type')) {

  class Custom_Post_Type {
  
    var $post_type = 'custom_post';
    var $editable = true;
    
    function setup_post($options) {
      $defaults = array(
        'label' => __('Custom Posts'),
        'singular_label' => __('Custom Post'),
        'public' => true,
        'publicly_queryable' => false,
        'show_ui' => $this->editable,
        'register_meta_box_cb' => array(&$this, 'edit_post'),
        'hierarchical' => true,
        'supports' => array(
          'revisions',
          'post-thumbnails'
        )
      );
      $this->post_options = array_merge($defaults, $options);
      if (!empty($options['supports'])) {
        $this->post_options['supports'] = array_merge($defaults['supports'], $options['supports']);
      }
      register_post_type($this->post_type, $this->post_options);
      add_action('edit_post', array(&$this, 'save_post'));
    }
    
    function setup_taxonomy($taxonomy, $options) {
      $taxonomy_id = "{$this->post_type}_$taxonomy";
      $defaults = array(
        'hierarchical' => true,
        'label' => __('AIB Taxonomy'),
        'query_var' => $taxonomy_id,
        'rewrite' => array('slug' => $taxonomy_id)
      );
      $taxonomy_options = array_merge($defaults, $options);
      register_taxonomy($taxonomy, $this->post_type, $taxonomy_options);
      if (empty($this->taxonomies)) {
        $this->taxonomies = array(
          $taxonomy_id => $taxonomy_options
        );
      } else {
        $this->taxonomies[$taxonomy_id] = $taxonomy_options;
      }
    }
    
    function initialize_taxonomy($taxonomy, $terms, $parent = null) {
      foreach ($terms as $term) {
        $this->create_term($taxonomy, $term, $parent);
      }
    }
    
    function create_term($taxonomy, $term, $parent = null) {
      $taxonomy_id = "{$this->post_type}_$taxonomy";
      $options = array();
      if (!empty($parent)) {
        $options['parent'] = $parent;
      }
      wp_insert_term($term, $taxonomy_id, $options);
    }
    
    function get($id, $key) {
      $values = get_post_custom_values($key, $id);
      if (count($values) == 0) {
        return null;
      } else {
        return array_pop($values);
      }
    }
    
    function set($id, $key, $value) {
      if ($this->get($id, $key) !== null) {
        update_post_meta($id, $key, $value);
      } else {
        add_post_meta($id, $key, $value);
      }
    }
    
  }
  
}

?>
