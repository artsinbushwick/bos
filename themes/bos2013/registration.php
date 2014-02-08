<?php
/*
Template Name: AIB Registration
*/

if (defined('AIB_PATH')) {
  require_once AIB_PATH . "/registration/setup.php";
  get_header();
  aib_registration();
  get_footer();
} else {
  echo "Is the Arts In Bushwick plugin enabled?";
}

?>
