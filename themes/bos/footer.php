      <footer>
        <div class="row1">
          <a href="/">
            <img src="<?php echo get_template_directory_uri(); ?>/images/footer_aib.png" alt="Arts In Bushwick">
          </a>
          <div class="border"></div>
          <div class="about">
            We are an all volunteer organization; our mission is to promote &amp; support local artists, serve the community at large through arts education, creative accessibility, organizing all residents &amp; stakeholders in Bushwick to fight development-driven displacement &amp; work toward an integrated, sustainable community.
          </div>
        </div>
        <div class="row2">
          <?php
          
          // To edit the list of links in the footer, go to Menus in the WP admin
          
          $html = wp_nav_menu(array(
            'theme_location' => 'footer-menu',
            'walker' => new BOS_Walker_Nav_Menu(),
            'echo' => false
          ));
          if (preg_match_all('/<li>/', $html, $matches)) {
            $count = count($matches[0]);
            wp_nav_menu(array(
              'theme_location' => 'footer-menu',
              'walker' => new BOS_Walker_Nav_Menu(),
              'container' => false,
              'count' => $count,
              'columns' => 3
            ));
          }
          
          ?>
          <br class="clear">
        </div>
        <div class="row3">
          Designed by <a href="http://jefflagasca.com/">Jeff Lagasca</a
          > / 
          Developed by <a href="http://phiffer.org/">Dan Phiffer</a>
        </div>
      </footer>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
    var theme_url = '<?php echo get_template_directory_uri(); ?>';
    var header_count = <?php echo bos_header_count(); ?>;
    </script>
    <script src="<?php echo get_template_directory_uri(); ?>/bos.js"></script>
    <?php wp_footer(); ?>
  </body>
</html>
