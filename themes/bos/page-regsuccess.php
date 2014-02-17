<?php
/*
Template Name: Registration Success
*/

global $wpdb, $blog_id, $userdata, $userdata;

get_currentuserinfo();

if (is_object($userdata) && !empty($userdata->ID)) {
  $post_id = get_usermeta($userdata->ID, 'aib_post_' . AIB_EVENT);
  if (!empty($post_id)) {
    $wpdb->update('aib_listing', array(
      'squared_up' => 1
    ), array('post_id' => $post_id, 'site_id' => $blog_id));
  }
}

require __DIR__ . '/index.php';

?>
