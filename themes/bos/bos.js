(function($) {

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

$(document).ready(function() {
  set_header();
  resize_logo();
  setup_menu();
  setup_sponsors();
  $(window).resize(resize_logo);
});
    
})(jQuery);
