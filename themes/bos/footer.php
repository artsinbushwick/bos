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
