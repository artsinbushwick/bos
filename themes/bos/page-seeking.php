<?php
/*

Template Name: Seeking Form

*/

wp_enqueue_style('aib_seeking', get_template_directory_uri() . '/seeking/css/seeking.css');
wp_enqueue_script('aib_mootools', get_template_directory_uri() . '/registration/js/mootools.js');
wp_enqueue_script('aib_mootools_more', get_template_directory_uri() . '/registration/js/mootools-more.js');
wp_enqueue_script('aib_registration', get_template_directory_uri() . '/registration/js/registration.js');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $method = $_POST['task'];
  if (method_exists($bos->seeking, $method)) {
    $bos->seeking->$method();
  }
}

get_header();

if (empty($_GET['contact'])) {
  if (have_posts()) {
    the_post();
    $url = get_permalink();
    echo '<div id="main"><div class="post">';
    echo "<h3><a href=\"$url\">Need Space? Have Space?</a></h3>";
    echo '<div class="content">';
    $bos->seeking->feedback();
    the_content();
    echo '</div>';
    edit_post_link('Edit post');
    echo '</div>';
  }
}

echo '<div id="seeking">';
if (empty($_GET['contact'])) {
  get_template_part('seeking/form', 'post');
  get_template_part('seeking/listings');
} else {
  get_template_part('seeking/form', 'contact');
}

echo '</div></div>';

get_sidebar();

echo '<br class="clear">';

get_footer();

?>
