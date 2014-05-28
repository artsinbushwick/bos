(function($) {

var map_locations = {};
var masonry;
    
function set_header() {
  var header_num = Math.floor(Math.random() * header_count) + 1;
  var url = theme_url + '/headers/header' + header_num + '.jpg';
  $('body').css('background-image', 'url(' + url + ')');
}

function resize_logo() {
  if (window.innerWidth < 630) {
    var width = window.innerWidth;
    var height = Math.round(window.innerWidth * 0.073015);
    var bg_width = 1920 * height / 70;
    $('header h1 a').css({
      width: width + 'px',
      height: height + 'px',
      'background-size': bg_width + 'px ' + height + 'px'
    });
    $('header h1').css({
      display: 'block',
      top: (125 - height - 15) + 'px'
    });
  } else {
    $('header h1 a').css({
      width: 'auto',
      height: (window.innerWidth >= 960) ? 70 : 46
    });
  }
}

function setup_menu() {
  $('nav.primary .toggle').click(function(e) {
    e.preventDefault();
    $('nav.primary').toggleClass('active');
  });
}

function setup_sponsors() {
  if (!$('.sponsors').length || !$('.sponsors a').length) {
    return;
  }
  var tiers = $('.sponsors').length;
  
  var tier_index = [];
  for (var i = 0; i < tiers; i++) {
    show_sponsor(i, 0);
    tier_index[i] = 0;
  }
  
  var tier = 0;
  setInterval(function() {
    var sponsors = $('.sponsors')[tier];
    var index = tier_index[tier];
    var count = $(sponsors).find('a').length;
    var visible = $(sponsors).find('a')[index];
    index = (index + 1) % count;
    tier_index[tier] = index;
    $(visible).fadeOut(400, function() {
      show_sponsor(tier, index);
      tier = (tier + 1) % tiers;
    });
  }, Math.round(5000 / tiers));
  $('.sponsors a.active').fadeIn();
}

function show_sponsor(tier, index) {
  var sponsors = $('.sponsors')[tier];
  var link = $(sponsors).find('a')[index];
  $(link).fadeOut(0);
  if ($(link).hasClass('pending')) {
    $(link).removeClass('pending');
    var src = $(link).attr('data-image');
    $('<img src="' + src + '">').load(function() {
      $(this).appendTo(link);
      $(link).fadeIn();
    });
  } else {
    $(link).fadeIn();
  }
}

function setup_directory() {
  if ($('#directory-map').length > 0) {
    setup_directory_map();
  }
  $('.images .feature img').load(function() {
    $(this).fadeIn();
  });
  if ($('.listing .images').length > 0) {
    $('.images .thumb').click(function(e) {
      e.preventDefault();
      $('.images .selected').removeClass('selected');
      $(this).addClass('selected');
      $('.images .feature img')[0].src = $(this).attr('href');
    });
  }
  $('.images .feature').click(function(e) {
    e.preventDefault();
    if ($('.images .thumb').length == 0) {
      return;
    }
    var curr_src = $(this).find('img')[0].src;
    var curr_index, next_index;
    $('.images .thumb').each(function(i, thumb) {
      if ($(thumb).attr('href') == curr_src) {
        curr_index = i;
      }
    });
    if (curr_index == $('.images .thumb').length - 1) {
      next_index = 0;
    } else {
      next_index = curr_index + 1;
    }
    var next_thumb = $('.images .thumb')[next_index];
    $('.images .feature img')[0].src = $(next_thumb).attr('href');
    $('.images .selected').removeClass('selected');
    $(next_thumb).addClass('selected');
  });
  setup_search();
}

function load_pending_image(listing) {
  if ($(listing).find('.image-pending')) {
    var image = $(listing).find('.image-pending');
    var src = image.attr('data-src');
    var url = $(listing).find('h4 a').attr('href');
    var link = '<a href="' + url + '" class="title" target="_blank">';
    image.append(link + '<img src="' + src + '"></a>');
    image.removeClass('image-pending');
  }
}

function setup_directory_map() {
  var zoom = $(window).width() > 629 ? 14 : 13;
  var map_resize = function() {
    if ($(window).width() < 630) {
      $('#directory-map').css('width', $(window).width());
    } else {
      $('#directory-map').css('width', 'auto');
    }
  }
  map_resize();
  $(window).resize(map_resize);
  $('#directory-map').addClass('zoom-wide');
  var map = L.map('directory-map')
             .setView([40.7011083, -73.9218508], zoom)
             .addLayer(L.mapbox.tileLayer('examples.map-20v6611k', {
               detectRetina: true
             }));
  map.on('zoomend', function() {
    $('#directory-map').removeClass('zoom-wide');
    $('#directory-map').removeClass('zoom-close');
    zoom = Math.round(map.getZoom());
    if (zoom > 15) {
      $('#directory-map').addClass('zoom-close');
    } else {
      $('#directory-map').addClass('zoom-wide');
    }
  });
  map.on('locationfound', function(e) {
    map.fitBounds(e.bounds);
  });
  map.scrollWheelZoom.disable();
  if (!navigator.geolocation) {
    $('#geolocate').remove();
  } else {
    $('#geolocate').click(function(e) {
      e.preventDefault();
      map.locate();
    });
  }
  directory_map_setup_markers(map);
  setTimeout(function() {
    if ($(window).width() < 630) {
      window.scrollTo(1, 126);
    }
    masonry = new Masonry($('#directory-listings')[0], {
      columnWidth: 290,
      itemSelector: '.listing'
    });
  }, 0);
  $('#directory-listings').on('click', function(e) {
    var listing, location;
    if ($(e.target).hasClass('title')) {
      return;
    }
    if ($(e.target).hasClass('listing')) {
      listing = e.target;
    } else if ($(e.target).closest('.listing').length > 0) {
      listing = $(e.target).closest('.listing')[0];
    }
    if ($(e.target).hasClass('location')) {
      location = e.target;
    } else if ($(e.target).closest('.location').length > 0) {
      location = $(e.target).closest('.location')[0];
    }
    if (location) {
      var location_id = $(location).attr('data-location');
      var marker = map_locations[location_id].map_marker;
      map.setView(marker.getLatLng(), 18);
      marker.openPopup();
      if (!$(listing).hasClass('pinned')) {
        $(listing).prependTo($('#directory-listings'));
        $(listing).addClass('pinned');
      }
    } else if (listing && !$(listing).hasClass('selected')) {
      $(listing).addClass('selected');
      load_pending_image(listing);
    } else if (listing && $(e.target).hasClass('close')) {
      $(listing).removeClass('selected');
      if ($(listing).hasClass('pinned')) {
        $(listing).removeClass('pinned');
        var num_pinned = $('#directory-listings .pinned').length;
        if (num_pinned > 0) {
          var last_pinned = $('#directory-listings .pinned')[num_pinned - 1];
          $(last_pinned).after(listing);
        }
      }
    }
  });
  $('#directory-map').on('click', function(e) {
    if ($(e.target).hasClass('location')) {
      var num = $(e.target).attr('data-marker');
      var query = 'bos:' + num.toLowerCase();
      if (use_history_pushstate()) {
        var base_url = location.protocol + '//' + location.host + location.pathname;
        history.pushState({}, '', base_url + '?q=' + query);
        e.preventDefault();
      }
      /*var id = $(e.target).attr('data-id');
      $('#directory-listings .location' + id).each(function(i, listing) {
        if (!$(listing).hasClass('pinned')) {
          $(listing).prependTo('#directory-listings');
          $(listing).addClass('pinned');
          $(listing).addClass('selected');
          load_pending_image(listing);
        }
      });*/
    }
  });
  $('#filter-toggle').click(function(e) {
    e.preventDefault();
    $('#filter-options').toggleClass('visible');
  });
  $('.filter-option').change(function(e) {
    var media = [];
    var attrs = [];
    $('.filter-option').each(function(i, option) {
      var value = $(option).attr('data-value');
      if (value.substr(0, 5) == 'media' && option.checked) {
        media.push(value.substr(6));
      } else if (value.substr(0, 10) == 'attributes' && option.checked) {
        attrs.push(value.substr(11));
      }
    });
    media = media.join(',');
    attrs = attrs.join(',');
    $('#media').val(media);
    $('#attrs').val(attrs);
    $('#directory')[0].className = '';
    if (media != '' || attrs != '') {
      directory_filter(media, attrs);
    }
  });
}

function setup_search() {
  if (!use_history_pushstate()) {
    return;
  }
  var base_url = location.protocol + '//' + location.host + location.pathname;
  $('#search').on('submit', function(e) {
    e.preventDefault();
    var filter = '';
    if ($('#media').val() != '') {
      filter += '&media=' + $('#media').val();
    }
    if ($('#attrs').val() != '') {
      filter += '&attrs=' + $('#attrs').val();
    }
    history.pushState({}, '', base_url + '?q=' + encodeURIComponent($('#query').val()).replace('%3A', ':') + filter);
    directory_search($('#query').val());
    if (filter != '') {
      directory_filter($('#media').val(), $('#attrs').val());
    }
    return false;
  });
  $(window).on("popstate", function() {
    var query = location.href.match(/q=([^&#]+)/);
    if (query) {
      query = decodeURIComponent(query[1]);
      $('#query').val(query);
      directory_search(query);
    }
    var media = location.href.match(/media=([^&#]+)/);
    var attrs = location.href.match(/attrs=([^&#]+)/);
    if (media || attrs) {
      if (media) {
        media = decodeURIComponent(media[1]);
        directory_set_filter('media', media);
      }
      if (attrs) {
        attrs = decodeURIComponent(attrs[1]);
        directory_set_filter('attrs', attrs);
      }
      directory_filter(media, attrs);
    }
  });
}

function directory_default_result_summary() {
  var count = $('#directory-listings .listing').length;
  var plural = (count == 1) ? ' found' : 's in random order';
  $('#result-summary').html(count + ' show' + plural);
}

function directory_search(query) {
  if (query == '') {
    return;
  }
  var data = {
    action: 'bos_directory_search',
    query: query
  };
  $.get(ajaxurl, data, function(response) {
    $('#directory-listings').addClass('search-results');
    $('#directory-listings .search-match').removeClass('search-match');
    $('#directory-map .search-match').removeClass('search-match');
    for (var i in response.ids) {
      var listing = '#listing' + response.ids[i];
      $(listing).addClass('search-match');
      if ($(listing).length > 0) {
        var location_id = $(listing)[0].className.match(/location\d+/);
        if (location_id) {
          $('#directory-map .' + location_id[0]).addClass('search-match');
          $('#directory-map').addClass('search-results');
        }
      }
    }
    $('#directory-map').addClass('search-results');
    var count = response.ids.length;
    var plural = (count == 1) ? '' : 's';
    var clear_url = location.pathname;
    $('#result-summary').html(count + ' show' + plural + ' found for “' + response.query + '” <a href="' + clear_url + '" id="search-clear">clear</a>');
    if (use_history_pushstate()) {
      $('#search-clear').click(function(e) {
        e.preventDefault();
        var base_url = location.protocol + '//' + location.host + location.pathname;
        history.pushState({}, '', base_url);
        $('#directory-listings, #directory-map').removeClass('search-results');
        $('#query').val('');
        directory_default_result_summary();
      });
    }
  });
}

function directory_filter(media, attrs) {
  var terms = [];
  if (typeof media == 'string' && media != '') {
    media = media.split(',');
    for (var i in media) {
      terms.push('media-' + media[i]);
    }
  }
  if (typeof attrs == 'string' && attrs != '') {
    attrs = attrs.split(',');
    for (var i in attrs) {
      terms.push('attributes-' + attrs[i]);
    }
  }
  for (var i in terms) {
    $('#directory').addClass(terms[i]);
  }
  $('#directory').addClass('filter-results');
}

function directory_set_filter(id, value) {
  
}

function directory_map_setup_markers(map) {
  directory_map_show_markers(map, directory_hubs, 'hub');
  directory_map_show_markers(map, directory_locations, 'location');
  var listings = $('#directory-listings').children();
  listings.sort(function(a, b) {
    return (Math.random() > 0.5) ? -1 : 1;
  });
  $('#directory-listings').html(listings);
  $('#directory-listings').append('<br class="clear">');
  $('#directory').removeClass('loading');
  if ($('#query').val() != '') {
    directory_search($('#query').val());
  }
  directory_default_result_summary();
}

function directory_map_show_markers(map, locations, marker_class) {
  for (var i in locations) {
    directory_map_marker(map, locations[i], marker_class);
    directory_listings(locations[i], marker_class);
  }
}

function directory_map_marker(map, location, marker_class) {
  var media = [];
  var attributes = [];
  for (var i in location.listings) {
    var listing = location.listings[i];
    if (typeof listing.media == 'object') {
      for (var j in listing.media) {
        media.push(' media-' + listing.media[j]);
      }
    }
    if (typeof listing.attributes == 'object') {
      for (var j in listing.attributes) {
        attributes.push(' attributes-' + listing.attributes[j]);
      }
    }
  }
  var zoffset = (marker_class == 'hub') ? 300 : 0;
  var latlng = [location.lat, location.lng];
  var classname = 'directory-map-' + marker_class +
                  ' location' + location.id +
                  media.join('') + attributes.join('');
  location.map_marker = L.marker(latlng, {
    icon: L.divIcon({
      className: classname,
      html: location.marker,
      iconSize: null
    }),
    zIndexOffset: zoffset
  }).addTo(map)
    .bindPopup('<a href="#listings" data-marker="' + location.marker + '" class="location">' + location.address + '</a><br>' +
             location.listings.length + ' listing' +
             ((location.listings.length == 1) ? '' : 's'), {
    offset: [0, 0]
  });
  map_locations[location.id] = location;
}

function directory_listings(location, marker_class) {
  var html = '';
  for (var i in location.listings) {
    var listing = location.listings[i];
    listing.location = location;
    var link_open = '';
    var link_close = '';
    if (listing.url && listing.url != '#') {
      link_open = '<a href="' + listing.url + '" class="title" target="_blank">';
      link_close = '</a>';
    } else {
      continue;
    }
    var attributes = '';
    if (typeof listing.attributes === 'object') {
      for (var i in listing.attributes) {
        attributes += ' attributes-' + listing.attributes[i];
      }
    }
    var media = '';
    if (typeof listing.media === 'object') {
      for (var i in listing.media) {
        media += ' media-' + listing.media[i];
      }
    }
    html += '<div class="listing location' + listing.location_id + attributes + media + '" id="listing' + listing.post_id + '" ontouchstart="">' +
            '<h4>' + link_open + directory_listing_title(listing) + link_close + '</h4>' +
            '<div class="summary">' + listing.short_description + '</div>' +
            '<div class="details">' + directory_listing_details(listing, marker_class) + '</div>' +
            '<div class="close" ontouchstart="">&times;</div>' +
            '</div>';
  }
  $('#directory-listings').append(html);
}

function directory_listing_title(listing) {
  if (listing.primary_name &&
      listing[listing.primary_name] &&
      listing[listing.primary_name] != '') {
    return listing[listing.primary_name];
  } else if (listing.artists && listing.artists != '') {
    listing.primary_name = 'artists';
    return listing.artists;
  } else if (listing.artists && listing.organization != '') {
    listing.primary_name = 'organization';
    return listing.organization;
  } else if (listing.artists && listing.event_name != '') {
    listing.primary_name = 'event_name';
    return listing.artists;
  } else {
    return 'Untitled listing';
  }
}

function directory_listing_subtitle(listing) {
  var subtitle = [];
  var key = listing.primary_name;
  if (key != 'artists' && listing.artists) {
    subtitle.push(listing.artists);
  }
  if (key != 'organization' && listing.organization) {
    subtitle.push(listing.organization);
  }
  if (key != 'event_name' && listing.event_name) {
    subtitle.push(listing.event_name);
  }
  return subtitle.join(', ');
}

function directory_listing_details(listing, marker_class) {
  var details = '';
  var marker = '<div class="marker marker-' + marker_class + '">' + listing.location.marker + '</div>';
  var address_name = listing.location.address;
  var address_meta = '';
  if (listing.room_number && listing.room_number != '') {
    address_name += ', ' + listing.room_number;
  }
  if (listing.space_name && listing.space_name != '') {
    address_meta = address_name;
    address_name = listing.space_name;
  } else {
    address_meta = '&nbsp;';
  }
  details += '<a href="#map" class="location" data-location="' + listing.location.id + '">' +
               marker +
               '<span class="address-name">' + address_name + '</span>' +
               '<span class="address-meta">' + address_meta + '</span>' +
             '</a>';
  if (listing.image) {
    details += '<div class="image image-pending" data-src="' + listing.image + '"></div>';
  }
  if (listing.short_description) {
    details += '<div class="description">' + directory_listing_subtitle(listing) + '</div>';
  }
  return details;
}

function use_history_pushstate() {
  return typeof history === 'object' &&
         typeof history.pushState === 'function';
}

$(document).ready(function() {
  set_header();
  resize_logo();
  setup_menu();
  setup_sponsors();
  setup_directory();
  $(window).resize(resize_logo);
});
    
})(jQuery);

// * iOS zooms on form element focus. This script prevents that behavior.
// * <meta name="viewport" content="width=device-width,initial-scale=1">
//      If you dynamically add a maximum-scale where no default exists,
//      the value persists on the page even after removed from viewport.content.
//      So if no maximum-scale is set, adds maximum-scale=10 on blur.
//      If maximum-scale is set, reuses that original value.
// * <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=2.0,maximum-scale=1.0">
//      second maximum-scale declaration will take precedence.
// * Will respect original maximum-scale, if set.
// * Works with int or float scale values.
function cancelZoom()
{
    var d = document,
        viewport,
        content,
        maxScale = ',maximum-scale=',
        maxScaleRegex = /,*maximum\-scale\=\d*\.*\d*/;

    // this should be a focusable DOM Element
    if(!this.addEventListener || !d.querySelector) {
        return;
    }

    viewport = d.querySelector('meta[name="viewport"]');
    content = viewport.content;

    function changeViewport(event)
    {
        // http://nerd.vasilis.nl/prevent-ios-from-zooming-onfocus/
        viewport.content = content + (event.type == 'blur' ? (content.match(maxScaleRegex, '') ? '' : maxScale + 10) : maxScale + 1);
    }

    // We could use DOMFocusIn here, but it's deprecated.
    this.addEventListener('focus', changeViewport, true);
    this.addEventListener('blur', changeViewport, false);
}

// jQuery-plugin
(function($)
{
    $.fn.cancelZoom = function()
    {
        return this.each(cancelZoom);
    };

    // Usage:
    $('input:text,select,textarea').cancelZoom();
})(jQuery);
