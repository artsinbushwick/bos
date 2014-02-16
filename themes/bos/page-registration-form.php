<?php
/*

Template Name: Registration Form

*/

if (!empty($_GET['images'])) {
  get_template_part('registration/form', 'images');
} else {
  wp_enqueue_style('aib_registration', get_template_directory_uri() . '/registration/css/registration.css');
  wp_enqueue_script('aib_mootools', get_template_directory_uri() . '/registration/js/mootools.js');
  wp_enqueue_script('aib_mootools_more', get_template_directory_uri() . '/registration/js/mootools-more.js');
  //wp_enqueue_script('aib_slimbox', get_template_directory_uri() . '/registration/js/slimbox.js');
  wp_enqueue_script('aib_registration', get_template_directory_uri() . '/registration/js/registration.js');
  get_header();
  echo '<div id="registration">';
  
  if (have_posts()) {
    the_post();
  }
  
  if ($bos->registration->in_progress()) {
    get_template_part('registration/form', 'edit');
  } else {
    get_template_part('registration/form', 'login');
  }
  
  $bos->registration->tokens_status();
  
  echo '</div>';
  
  get_footer();
}

?>
