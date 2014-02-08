<div id="primary" class="widget-area" role="complementary">
<ul class="sidebar_bos">

<?php if ( ! is_front_page() ) {?>

  <li class="unit_directory" style="display: none">
    <a href="/bos2013/directory">
     <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sidebar_directory.png" alt="Go to the Bushwick Open Studios 2013 Show Directory" />
    </a>
    
    <p>
      <h4>Missed the print registration deadline?</h4>
      You can still <strong style="color: #000">register</strong> through May 31<sup>nd</sup> for a listing in our online
      directory. <strong><a href="/bos2013/register">Register Now &raquo;</a></strong>
    </p>
    
  </li>

<?php }?>

  <li class="unit_sponsor list_sponsors">
  
    <a href="http://artsinbushwick.org/bos2013/sponsor-sidebar-list/">
     <h3><span style="color:#000;font-size:19px;">Thank You to All Our</span><br /><span style="color:#ea288f;font-size:22px;margin-bottom:12px;">BOS2013 Sponsors</span></h3>
    </a>
  
<script>
$(document).ready(function(){
  $('ul.sponsor_list').each(function(){
    var $ul = $(this);
    var $liArr = $ul.children('li');
    $liArr.sort(function(a,b){
      var temp = parseInt( Math.random()*10 );
      var isOddOrEven = temp%2;
      var isPosOrNeg = temp>5 ? 1 : -1;
      return( isOddOrEven*isPosOrNeg );
    })
  .appendTo($ul);
  });
  $('.tumblr_post img').attr('width', '270');
});
</script>
    
    <?php $page_id = 3088;
			$page_data = get_page( $page_id ); 
			$content = apply_filters('the_content', $page_data->post_content);
			$title = $page_data->post_title;
				echo $content;
		?>
    
    <h4>
    <a href="/bos2013/sponsor">&laquo; Become a Sponsor</a>
    &bull;
    <a href="/bos2013/sponsor-sidebar-list/">All sponsors &raquo;</a>
    </h4>
  </li> 

  <li class="unit_socialmedia">
  	<img src="<?php bloginfo('stylesheet_directory'); ?>/images/sidebar_socialmedia.png" alt="Registration Open Now!" />
  	<!--<div><h3>Latest on the Blog</h3>
    <?php
      
      // The Query
      $the_query = new WP_Query( 'post_type=post' );
      $the_query = new WP_Query( 'posts_per_page=1' );

      // The Loop
      while ( $the_query->have_posts() ) : $the_query->the_post();
 	      echo '<h4 class="post-title">';
	      the_title();
 	      echo '</h4>';
 	      echo '<div>';
	      the_excerpt();
	      echo '</div>';
      endwhile;

      // Reset Post Data
    wp_reset_postdata(); ?>
	<a href="/bos2013/blog" class="link_viewmore">Go to blog &raquo;</a></div>-->
  	
  	<h3>Upcoming Events/Meetings</h3>
  	<div class="events">
    <?php $page_id = 81;
			$page_data = get_page( $page_id ); 
			$content = apply_filters('the_content', $page_data->post_content);
			$title = $page_data->post_title;
				echo $content;
		?>
		</div>
  	
  	<h4><a href="https://groups.google.com/group/arts-in-bushwick-2" target="new" style="color: #000">Join the Google Group &raquo;</a></h4>
  	<h4><a href="http://visitor.constantcontact.com/manage/optin/ea?v=001nkQbX5l1Qs9P_67_Rm6M0A%3D%3D" target="new" style="color: #000">Subscribe to Our Mailing List &raquo;</a></h4>
  	<br style="padding: 0 0 15px 0" />
  	<h3>Find us Online!</h3>
    <?php $page_id = 218;
			$page_data = get_page( $page_id ); 
			$content = apply_filters('the_content', $page_data->post_content);
			$title = $page_data->post_title;
				echo $content;
		?>
  </li>
 
  </li>
</ul>
</div>