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

$(document).ready(function() {
  set_header();
  resize_logo();
  setup_menu();
  $(window).resize(resize_logo);
});
    
})(jQuery);
