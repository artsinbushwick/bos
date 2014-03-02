<?php

if (!class_exists('Form')) {

  class Form {

    function add_action($hook, $method = null, $priority = 10, $args = 1) {
      if (empty($method)) {
        $method = $hook;
      }
      add_action($hook, array($this, $method), $priority, $args);
    }

    function add_filter($hook, $method = null, $priority = 10, $args = 1) {
      if (empty($method)) {
        $method = $hook;
      }
      add_filter($hook, array($this, $method), $priority, $args);
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

}

?>
