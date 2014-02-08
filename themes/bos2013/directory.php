<?php
/*
Template Name: Directory
*/

$dir = dirname(get_bloginfo('stylesheet_url'));
wp_enqueue_script('gmaps', 'http://maps.google.com/maps/api/js?sensor=false', array(), '1');
wp_enqueue_style('directory', "$dir/directory.css");
wp_enqueue_script('directory', "$dir/directory.js", array('gmaps'));
//wp_enqueue_script('colorbox', "$dir/colorbox/jquery.colorbox-min.js");
//wp_enqueue_style('colorbox', "$dir/colorbox/colorbox.css");

require_once AIB_PATH . '/functions.php';

if (!empty($_GET['listing'])) {
  include dirname(__FILE__) . '/listing.php';
  exit;
}

if (!empty($_GET['geocode'])) {
  include dirname(__FILE__) . '/geocode.php';
}

$perpage = 10;
if (!empty($_GET['perpage']) && (is_numeric($_GET['perpage']) || $_GET['perpage'] == 'All')) {
  if ($_GET['perpage'] == 'All') {
    $perpage = 10000;
  } else {
    $perpage = $_GET['perpage'];
  }
}
$posts = aib_get_posts();

?>
<?php get_header(); ?>
<form action="./" name="directory" id="directory" data-perpage="<?php echo $perpage; ?>">
  <div id="filters">
    <?php aib_search(); ?>
    <div class="first column">
      <?php aib_day_filter(); ?>
      <?php aib_media_filter(); ?>
    </div>
    <div class="column">
      <?php aib_feature_filter(); ?>
    </div>
    <input type="submit" value="Search" id="search-button" />
  </div>
  
  
  <div id="results">
    <h2><?php echo count($posts); ?> Shows</h2>
    <?php if (count($posts) == 0) { ?>
      Try changing your search criteria to find matching shows
    <?php } else { ?>
      Results ordered randomly
    <?php } ?>
  </div>
  
  <?php if (count($posts) > 0) { ?>
  
  <div id="map"></div>
  
  <?php } ?>
  
  
  <?php foreach ($posts as $num => $post) { ?>
    <?php if ($num % $perpage == 0) { ?>
      <?php if ($num != 0) { ?>
        </div>
      <?php } ?>
      <div id="page<?php echo ($num / $perpage + 1); ?>" class="page<?php if ($num == 0) { echo ' page-selected'; } ?>">
    <?php } ?>
    <div class="listing">
      <?php aib_marker($post); ?>
      <div class="info">
        <h4><a href="?listing=<?php echo $post->ID; ?>" class="show" data-latlng="<?php aib_latlng($post); ?>" data-address="<?php aib_address($post); ?>" data-location-id="<?php aib_location_id($post); ?>"><?php aib_primary_name($post); ?></a></h4>
        <h5><?php aib_subtitle($post); ?></h5>
        <p><?php aib_excerpt($post); ?></p>
      </div>
      <div class="image">
        <?php aib_thumbnail($post, false, true); ?>
      </div>
      <br class="clear" />
    </div>
    <?php if ($num == count($posts) - 1) { ?>
      </div>
    <?php } ?>
  <?php } ?>
  <?php aib_pagination($posts, $perpage); ?>
</form>
<br class="clear" />
<?php get_footer(); ?>

