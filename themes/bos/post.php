<div id="post-<?php the_ID(); ?>" class="post">
  <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
  <div class="content">
    <?php the_content(); ?>
  </div>
  <?php edit_post_link('Edit post'); ?>
</div>
