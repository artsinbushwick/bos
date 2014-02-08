jQuery(window).load(function() {
  var $ = jQuery;
  var map;
  var locations;
  var infowindow;
  var page = 1;
  var perPage = parseInt($('#directory').attr('data-perpage')) || 10;
  var numPages = Math.ceil($('a.show').length / perPage);
  var markerZIndex = 0;
  
  $('#page-select').change(function(e) {
    var select = $('#page-select')[0];
    gotoPage(select.options[select.selectedIndex].value);
    scrollTo(0, $('#results').position().top);
  });
  
  $('#perpage').change(function() {
    $('#directory')[0].submit();
  });
  
  $('.pagination .next').click(function(e) {
    if (page == numPages) {
      e.preventDefault();
      return;
    }
    page++;
    gotoPage(page);
  });
  
  $('.pagination .prev').click(function(e) {
    if (page == 1) {
      e.preventDefault();
      return;
    }
    page--;
    gotoPage(page);
  });
  
  if ($('#page-select').length > 0) {
    gotoPage(1);
  }
  
  //$("a[rel='images']").colorbox();
  
  if ($('#map').length > 0) {
    map = new google.maps.Map($("#map")[0], {
      zoom: 13,
      center: new google.maps.LatLng(40.70452163045151,-73.9258007619629),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    locations = {};
    infowindow = new google.maps.InfoWindow({
      content: '',
      maxWidth: 321
    });
    
    google.maps.event.addListener(map, 'zoom_changed', function() {
      for (var address in locations) {
        locations[address].marker.setIcon(getIconURL(locations[address]));
      }
    });
    
    var queue = [];
    $("a.show").each(function(i, link) {
      queue.push(link);
    });
    addLinkChunk(queue);  
    
    
    $(".listing .marker").each(function(i, img) {
      img.style.cursor = 'pointer';
      $(img).click(function() {
        var link = $(img).parents('.listing').find('a.show');
        var address = link.attr('data-address');
        markerZIndex++;
        locations[address].marker.setZIndex(markerZIndex);
        var latlng = link.attr('data-latlng').split(', ');
        var lat = latlng[0];
        var lng = latlng[1];
        map.setZoom(17);
        map.setCenter(new google.maps.LatLng(lat, lng));
        window.location = '#map';
      });
    });
  }
  
  function addLinkChunk(queue) {
    var num = queue.length > 10 ? 10 : queue.length;
    for (var i = 0; i < num; i++) {
      var link = queue.pop();
      addLink(link);
    }
    if (queue.length > 0) {
      setTimeout(function() {
        addLinkChunk(queue);
      }, 0);
    }
  }
  
  function addLink(link) {
    var address = $(link).attr('data-address');
    if (!locations[address]) {
      var latlng = $(link).attr('data-latlng').split(', ');
      var lat = latlng[0];
      var lng = latlng[1];
      var zIndex = markerZIndex;
      var location_num = $(link).attr('data-location-id');
      if (location_num.match(/^[A-Z]$/)) {
        zIndex += 1000;
      }
      markerZIndex++;
      
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        map: map,
        title: address,
        zIndex: zIndex
      });
      
      locations[address] = {
        number: location_num,
        marker: marker,
        shows: [link]
      };
      
      var url = getIconURL(locations[address]);
      var icon = new google.maps.MarkerImage(url);
      icon.printImage = url.replace(/\.png$/, '.gif');
      icon.mozPrintImage = url.replace(/\.png$/, '.gif');
      locations[address].marker.setIcon(icon);
      
      google.maps.event.addListener(marker, 'click', function() {
        var address = marker.getTitle();
        var content = [];
        for (var i in locations[address].shows) {
          var link = locations[address].shows[i];
          content.push('<a href="' + $(link).attr('href') + '">' + $(link).html() + '</a>');
        }
        infowindow.setContent('<div id="infowindow' + location_num + '" class="infowindow"><strong>' + address + '</strong><br />' + content.join(', ') + '</div>');
        infowindow.open(map, marker);
      });
    } else {
      locations[address].shows.push(link);
    }
  }
  
  function gotoPage(n) {
    $('.page.page-selected').removeClass('page-selected');
    $('#page' + n).addClass('page-selected');
    $('#page' + n + ' img.pending').each(function(i, img) {
      if ($(img).attr('data-src') != '') {
        $(img).attr('src', $(img).attr('data-src'));
        $(img).removeClass('pending');
      } else {
        $(img).parent('a').remove();
      }
    });
    if (n == numPages) {
      $('.pagination .next').addClass('disabled');
    } else {
      $('.pagination .next').removeClass('disabled');
    }
    if (n == 1) {
      $('.pagination .prev').addClass('disabled');
    } else {
      $('.pagination .prev').removeClass('disabled');
    }
    $('#page-select')[0].selectedIndex = n - 1;
  }
  
  function getIconURL(location) {
    var url = '/wp-content/themes/bos2013/images/markers/';
    
    if (location.number.match(/^[A-Z]$/)) {
    //if (location.BOS13number.match(/^[A-Z]$/)) {
      url += 'hub/';
    }
    
    if (map.getZoom() < 15) {
      url += '15';
    } else if (map.getZoom() > 14 && map.getZoom() < 17) {
      url += '30';
    } else if (map.getZoom() > 16) {
      url += '40';
    }
    
    if (map.getZoom() > 14) {
      //if (parseInt(location.number) > 0) {
      //  url += '-' + location.number;
      //} else if (location.number.match(/^[A-Z]$/)) {
      //  url += '-' + location.number.toLowerCase();
      //}
      
      if (parseInt(location.number) > 0) {
        url += '-' + location.number;
      } else if (location.number.match(/^[A-Z]$/)) {
        url += '-' + location.number.toLowerCase();
      }     
      
    }
    
    url += '.png';
    return url;
  }
  
});

