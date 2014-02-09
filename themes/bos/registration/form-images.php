<?php

require_once ABSPATH . '/wp-admin/includes/file.php';
require_once ABSPATH . '/wp-admin/includes/media.php';
require_once ABSPATH . '/wp-admin/includes/image.php';

global $blog_id, $post, $bos;
$page_slug = $post->post_name;
$post = $bos->registration->get_post();

$response = '';
if (isset($_POST['html-upload']) && !empty($_FILES)) {
  $id = media_handle_upload('async-upload', $_REQUEST['post_id']);
  if (is_wp_error($id)) {
    $response = '<ul><li>' . implode('</li><li>', $id->get_error_messages()) . '</li></ul>';
  }
}

$main_image = $wpdb->get_var("
  SELECT main_image
  FROM aib_listing
  WHERE post_id = $post->ID
    AND site_id = $blog_id
");

if (!empty($_GET['remove'])) {
  wp_delete_attachment($_GET['remove']);
  if ($_GET['remove'] == $main_image) {
    $wpdb->update('aib_listing', array(
      'main_image' => 0
    ), array('post_id' => $post->ID, 'site_id' => $blog_id));
  }
}

if (!empty($_GET['main'])) {
  global $wpdb;
  $wpdb->update('aib_listing', array(
    'main_image' => intval($_GET['main'])
  ), array('post_id' => $post->ID, 'site_id' => $blog_id));
  $main_image = $_GET['main'];
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Images</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/registration/css/images.css">
    <script src="<?php echo get_template_directory_uri(); ?>/registration/js/mootools.js"></script>
  </head>
  <body>
    <form action="<?php echo bloginfo('url'); ?>/<?php echo $page_slug; ?>/?images=1" method="post" enctype="multipart/form-data">
      <input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
      <?php wp_nonce_field('media-form'); ?>
      <?php if (!empty($response)) { ?>
        <p><?php echo $response; ?></p>
      <?php } ?>
      <input type="file" name="async-upload" />
      <input type="submit" name="html-upload" id="submit" value="Upload" />
    </form>
    <?php
    
    $images = get_children("post_type=attachment&post_mime_type=image&post_parent=$post->ID");
    $num = 0;
    global $post;
    $url = get_bloginfo('url') . '/' . $page_slug;
    
    foreach ($images as $image) {
      list($thumbnail, $width, $height) = wp_get_attachment_image_src($image->ID);
      list($fullsize) = wp_get_attachment_image_src($image->ID, 'large');
      echo "<div class=\"image\">";
      echo "<a href=\"$fullsize\" target=\"_blank\"><img src=\"$thumbnail\" alt=\"\" width=\"$width\" height=\"$height\" /></a>";
      if ($image->ID == $main_image || (empty($main_image) && $num == 0)) {
        echo "<strong>Main image</strong>";
      } else {
        echo "<a href=\"$url/?images=1&amp;main=$image->ID\">Set to main</a>";
      }
      echo " &middot; <a href=\"$url/?images=1&amp;remove=$image->ID\">Delete</a>";
      echo "</div>";
      $num++;
    }
    
    ?>
    <br class="clear" />
    <div class="info">
        <p><strong>Please upload images in JPEG format, maximum file size is <?php echo ini_get('upload_max_filesize'); ?>B.</strong> You may upload as many images as you would like.  Images will be
displayed with your listing on our website, but will not be in the
printed program.  Your main image will be displayed on the listing
summary page in thumbnail form (the same dimensions as what you see as
a thumbnail for the images you have uploaded here). All images, including the main image, will be resized for you. Your images, when uploaded, should be <strong>around 1000 pixels wide</strong>. You may edit, delete, or upload more images
at any time through BOS.</p>
      </div>
  </body>
</html>
