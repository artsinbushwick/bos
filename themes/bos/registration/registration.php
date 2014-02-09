<?php

// Return a escaped POST value, if one exists
if (!function_exists('v')) {
  function v($name) {
    if (!empty($_POST[$name])) {
      return esc_attr($_POST[$name]);
    } else {
      return '';
    }
  }
}

class AIB_Registration extends Theme {
  
  function __construct() {
    $this->add_action('after_switch_theme');
  }
  
  function after_switch_theme() {
    // Setup MySQL tables if they haven't already been created
    $this->setup_db_tables();
  }
  
  function setup_db_tables() {
    global $wpdb;
    $tables = array(
      'aib_listing',
      'aib_seeking',
      'aib_location',
      'aib_token'
    );
    foreach ($tables as $table) {
      $exists = $wpdb->query("DESCRIBE $table");
      if (empty($exists)) {
        $sql = file_get_contents(__DIR__ . "/db/$table.sql");
        $wpdb->query($sql);
      }
    }
  }
  
  function in_progress() {
    return false;
  }
  
  function text_input($name, $value = null, $options = null) {
    global $post;
    $type = 'text';
    $id = "input_$name";
    $class = 'text';
    if (!empty($post->$name)) {
      $value = esc_attr($post->$name);
    }
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    echo "<input type=\"$type\" name=\"$name\" value=\"$value\"$id$class />";
  }
  
  function textarea_input($name, $value = null, $options = null) {
    global $post;
    $id = "input_$name";
    $class = 'text';
    $rows = 3;
    $cols = 50;
    if (!empty($post->$name)) {
      $value = esc_html($post->$name);
    }
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    if (!empty($maxlength)) {
      $maxlength = " maxlength=\"$maxlength\"";
    }
    echo "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\"$id$class$maxlength>$value</textarea>";
  }
  
  function radio_input($name, $value, $options = null) {
    global $post;
    $id = "input_{$name}_$value";
    $class = 'radio';
    $checked = ($post->$name == $value);
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    if (!empty($checked)) {
      $checked = ' checked="checked"';
    }
    echo "<input type=\"radio\" name=\"$name\" value=\"$value\"$checked$id$class />";
  }
  
  function checkbox_input($name, $options = null) {
    global $post;
    $id = "input_$name";
    $class = 'checkbox';
    $checked = (!empty($post->$name));
    if ($options) {
      extract($options);
    }
    if (!empty($id)) {
      $id = " id=\"$id\"";
    }
    if (!empty($class)) {
      $class = " class=\"$class\"";
    }
    if (!empty($checked)) {
      $checked = ' checked="checked"';
    }
    echo "<input type=\"checkbox\" name=\"$name\" value=\"1\"$checked$id$class />";
  }
  
}
