<?php

if (!class_exists('Theme')) {

  class Theme {

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

  }

}

?>
