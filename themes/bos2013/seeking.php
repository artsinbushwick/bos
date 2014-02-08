<?php
/*
Template Name: AIB Seeking
*/

if (defined('AIB_PATH')) {
  require_once AIB_PATH . "/seeking/setup.php";
  get_header();
  aib_seeking();
  include( TEMPLATEPATH . '/sidebar_bos.php' );
  echo '<br class="clear" />';
  get_footer();
} else {
  echo "Is the Arts In Bushwick plugin enabled?";
}


?>
