<?php

wp_enqueue_script('mapbox', 'https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.js');
wp_enqueue_style('mapbox', 'https://api.tiles.mapbox.com/mapbox.js/v1.6.2/mapbox.css');
wp_enqueue_script('masonry');

get_header();

$hubs = get_option('directory_hubs');
$hubs = json_encode($hubs);
echo "<script>var directory_hubs = $hubs;</script>";

$locations = get_option('directory_locations');
$locations = json_encode($locations);
echo "<script>var directory_locations = $locations;</script>";

?>
<div id="directory" class="loading">
  <div id="map">
    <div id="directory-map">
      <div class="leaflet-control-container">
        <div class="leaflet-bottom leaflet-left">
          <div class="leaflet-bar leaflet-control">
            <a href="#" id="geolocate" title="Find me on the map"></a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="listings">
    <form id="search">
      <?php $query = (empty($_GET['q'])) ? '' : $_GET['q']; ?>
      <input type="text" name="q" id="query" value="<?php echo esc_attr($query); ?>" placeholder="Search Directory">
      <button type="submit" id="submit"></button>
      <div id="search-meta">
        <span id="result-summary"></span>
      </div>
      <div id="filter-options">
        <input type="hidden" id="media" name="media" value="<?php if (!empty($_GET['media'])) echo esc_attr($_GET['media']); ?>">
        <input type="hidden" id="attrs" name="attrs" value="<?php if (!empty($_GET['attrs'])) echo esc_attr($_GET['attrs']); ?>">
        <?php
        
        $css_selectors = array();
        
        $media = get_terms('media');
        foreach ($media as $m) {
          if ($m->parent == 0 && $m->name != 'Other') {
            if (empty($first_group)) {
              $first_group = true;
            } else {
              echo '</div>';
            }
            echo "<div class=\"filter-group\"><h4>$m->name</h4>";
          } else {
            $class = "media-$m->slug";
            echo "<label><input type=\"checkbox\" class=\"filter-option\" data-value=\"$class\"> <span class=\"label\">$m->name</span></label>\n";
            $css_selectors[] = "#directory.$class #directory-map .$class, #directory.$class #directory-listings .$class";
          }
        }
        
        ?>
        <?php
        
        $attributes = get_terms('attributes');
        echo '</div><div class="filter-group"><h4>By Attribute</h4>';
        foreach ($attributes as $a) {
          $class = "attrs-$a->slug";
          echo "<label><input type=\"checkbox\" class=\"filter-option\" data-value=\"$class\"> <span class=\"label\">$a->name</span></label>\n";
          $css_selectors[] = "#directory.$class #directory-map .$class, #directory.$class #directory-listings .$class";
        }
        
        // filter group
        echo '</div>';
        
        ?>
      </div>
      <span id="filter-status">Showing everything available</span>
      &middot; <a href="#" id="filter-toggle">filter</a>
    </form>
    <div id="directory-listings" class="loading">
    </div>
    <style>
    
    <?php echo implode(', ', $css_selectors); ?> {
      display: block;
    }
    
    #directory.filter-results #directory-listings.search-results .listing,
    #directory.filter-results #directory-map.search-results .leaflet-marker-icon {
      display: none;
    }
    
    #directory.filter-results #directory-listings.search-results .search-match,
    #directory.filter-results #directory-map.search-results .search-match {
      display: block;
    }
    
    </style>
  </div>
</div>
<?php

get_footer();

?>
