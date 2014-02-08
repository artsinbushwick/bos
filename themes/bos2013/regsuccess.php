<?php
/*
Template Name: AIB Registration Success
*/

require_once dirname(__FILE__) . '/page.php';

global $blog_id, $userdata, $userdata;

get_currentuserinfo();

if (is_object($userdata) && !empty($userdata->ID)) {
  $post_id = get_usermeta($userdata->ID, 'aib_post_' . AIB_EVENT);
  echo "<!-- Logged in -->\n";
  if (!empty($post_id)) {
    $wpdb->update('aib_listing', array(
      'squared_up' => 1
    ), array('post_id' => $post_id, 'site_id' => $blog_id));
    echo "<!-- All squared up -->";
  } else {
    echo "<!-- No listing available -->";
  }
}

?>
