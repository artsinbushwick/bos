document.id(window).addEvent('domready', function() {
  var $ = document.id;
  if ($('input_action_create')) {
    $('input_action_create').addEvent('change', registerActionUpdate);
    $('input_action_edit').addEvent('change', registerActionUpdate);
    registerActionUpdate();
  }
  if ($('time_specific')) {
    $('input_time_specific').addEvent('change', timeSpecificUpdate);
    timeSpecificUpdate();
  }
  if ($('excerpt_length')) {
    var check_excerpt_length = function() {
      var number = 140 - $('input_post_excerpt').value.length;
      $('excerpt_length').set('text', number + ' characters left');
    }
    check_excerpt_length.periodical(100);
  }
  if ($('media-other')) {
    $('media-other').inject($('media-6'), 'after');
  }
  if ($('paypal')) {
    $('paypal').position({
      relativeTo: 'payment-info',
      position: 'bottomLeft'
    });
    /*$('paypal_button').addEvent('click', function() {
      $('paypal_form').send();
    });*/
  } else if ($('volunteer_message')) {
    $('volunteer_message').setStyle('display', 'none');
  }
  
  // Faux textarea MAXLENGTH attribute
  // Thanks to Josh Stodola's post to StackOverflow
  
  var txts = document.getElementsByTagName('TEXTAREA') 

  for(var i = 0, l = txts.length; i < l; i++) {
    if(/^[0-9]+$/.test(txts[i].getAttribute("maxlength"))) { 
      var func = function() { 
        var len = parseInt(this.getAttribute("maxlength"), 10); 

        if(this.value.length > len) { 
          this.value = this.value.substr(0, len); 
          return false; 
        } 
      }

      txts[i].onkeyup = func;
      txts[i].onblur = func;
    } 
  }
  
});

function registerActionUpdate() {
  var $ = document.id;
  var slide = getSlide('register_options');
  if ($('input_action_create').checked) {
    $('submit_login').setStyle('display', 'none');
    $('submit_register').setStyle('display', 'block');
    slide.slideIn();
  } else {
    $('submit_login').setStyle('display', 'block');
    $('submit_register').setStyle('display', 'none');
    slide.slideOut();
  }
}

function timeSpecificUpdate() {
  var $ = document.id;
  var slide = getSlide('time_specific');
  if ($('input_time_specific').checked) {
    slide.slideIn();
  } else {
    slide.slideOut();
  }
}

function editPaymentUpdate() {
  var $ = document.id;
  var volunteerSlide = getSlide('volunteer_message');
  var paypalSlide = getSlide('paypal');
  if ($('input_payment_method_volunteer').checked) {
    volunteerSlide.slideIn();
    paypalSlide.slideOut();
  } else {
    volunteerSlide.slideOut();
  }
  if ($('input_payment_method_paypal').checked) {
    volunteerSlide.slideOut();
    paypalSlide.slideIn();
  } else {
    paypalSlide.slideOut();
  }
}

function getSlide(id) {
  var $ = document.id;
  var slide = $(id).retrieve('slide');
  if (!slide) {
    slide = new Fx.Slide(id, {
      duration: 'short'
    });
    $(id).store('slide', slide);
  }
  return slide;
}

