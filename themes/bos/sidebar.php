<div id="sidebar">
  <div class="sponsors">
    <?php
    
    $sponsorship = get_page_by_path('sponsorship');
    if (preg_match_all('/<a href="([^"]+)">\s*<img[^>]+src="([^"]+)/ms', $sponsorship->post_content, $matches)) {
      foreach ($matches[1] as $index => $url) {
        $img = $matches[2][$index];
        echo "<a href=\"$url\" data-image=\"$img\" class=\"pending\"></a>\n";
      }
    }
    
    ?>
  </div>
  <?php dynamic_sidebar('sidebar'); ?>
</div>
