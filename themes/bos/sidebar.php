<div id="sidebar">
  <div class="widget">
    <?php
    
    $sponsorship = get_page_by_path('sponsorship');
    echo apply_filters('the_content', $sponsorship->post_content);
    
    ?>
  </div>
  <?php dynamic_sidebar('sidebar'); ?>
</div>
