<div id="sidebar">
  <?php
  
  $sponsorship = get_page_by_path('sponsorship');
  $posts = get_posts(array(
    'post_parent' => $sponsorship->ID,
    'post_type' => 'page',
    'orderby' => 'menu_order',
    'order' => 'ASC'
  ));
  
  foreach ($posts as $post) { ?>
    <div class="sponsors">
      <div class="inner">
        <?php
        
        $regex = '/<a href="([^"]+)">\s*<img[^>]+src="([^"]+)/ms';
        if (preg_match_all($regex, $post->post_content, $matches)) {
          foreach ($matches[1] as $index => $url) {
            $img = $matches[2][$index];
            echo "<a href=\"$url\" data-image=\"$img\" class=\"pending\"></a>\n";
          }
        }
    echo "</div></div>\n";
  }
  
  ?>
  <?php dynamic_sidebar('sidebar'); ?>
</div>
