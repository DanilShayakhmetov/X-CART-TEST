/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

$('.desktop-header').affix({
  offset: {
    top: function () {
        return 140;
    },
  }
});

$('.mobile_header .nav').affix({
  offset: {
    top: function () {
        return 140;
    },
  }
});

$(document).on('click', '#header-area .dropdown .dropdown-menu', function (e) {
  e.stopPropagation();
});

var panel = $('.header_search-panel');
panel.on('hidden.bs.collapse', function() {
	$(this).siblings('a').addClass('collapsed');
});

panel.on('show.bs.collapse', function() {
  $(this).siblings('a').removeClass('collapsed');
});

$(document).on('click', '.header_search a', function() {
  if ($(this).hasClass('shown')) {
    panel.find('input[name="substring"]').focus();
  }
});

function searchPanelToggle(event) {
  if(!$(event.target).closest('.simple-search-box').length &&
      !$(event.target).is('.simple-search-box')) {
    if (panel.hasClass('in')) {
      panel.collapse('hide');
      panel.find('input[name="substring"]').blur();
    }
  }
}

if ('ontouchstart' in document.documentElement) {
  $(document).on('touchstart', function(event) {
    searchPanelToggle(event);
  });
} else {
  $(document).click(function(event) {
    searchPanelToggle(event);
  });
}

$('.simple-search-box').focusin(function () {
  $(this).addClass('focus');
}).focusout(function () {
  $(this).removeClass('focus');
}).click(function () {
  $(this).find('input[name="substring"]').focus();
});

// minicart
function materializeMinicart() {
  var viewportWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

  // mobile view
  if (viewportWidth < 992) {
    switchMinicarts('.mobile_header .lc-minicart-placeholder');
  } else if ($('.desktop-header').find('.lc-minicart').length < 1) {
    switchMinicarts('.desktop-header .lc-minicart-placeholder');
  }
};

function switchMinicarts(target) {
  var placeholder = document.createElement('div');
  $(placeholder).addClass('lc-minicart-placeholder');
  $('.lc-minicart').after(placeholder);

  var cartElement = $('.lc-minicart').detach();

  $(target).replaceWith(cartElement);
}

materializeMinicart();

$(window).resize(materializeMinicart);
