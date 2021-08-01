/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Panel tour
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/* https://github.com/callahanrts/tiny-tour */

function openTour()
{
  window.tour = new Tour({
    next: window.tourData.next,
    done: window.tourData.done,
    steps: window.tourSteps,
    padding: 5,
  });

  var currentStep = 0;

  tour.override('showStep', function(self, step) {
    currentStep = step.step;

    var wrapper = this.el;
    if (step.position.includes('content-scroll')) {
      $('html, body').animate(
        {
          scrollTop: $(step.element).offset().top - ($(window).height() / 2 - $(step.element).height() / 2)
        }, 500, function() {
          showStep(self, step, wrapper);
          switchScroll(true);
        }
      );
    } else {
      showStep(self, step, wrapper);
      switchScroll(false);
    }
  });

  tour.override('nextStep', function(self) {
    $('.themetweaker-panel').addClass('ttour-active ttour-inline');

    self();

    if (this.current === 4) {
      tourClose(this.el);

      document.location = URLHandler.buildURL({
        base: xliteConfig.admin_script,
        target: 'banner_rotation',
      });
    }
  });

  tour.override('end', function(self) {});

  $(window).resize(function() {
    clearTimeout(this.id);
    this.id = setTimeout(function () {
      window.tour.showStep(window.tourSteps[currentStep - 1]);
    }, 300);
  });

  tour.start();
}

function showStep(self, step, wrapper)
{
  $(wrapper).children('.ttour-overlay').removeClass(function (index, css) {
    return (css.match (/\btt-overlay-\S+/g) || []).join(' ');
  }).addClass('tt-overlay-' + step.step);

  if (step.step === 4) {
    $(step.element).addClass('tourBanners');
    $('.themetweaker-panel').removeClass('ttour-inline');
  }

  self(step);

  var close = document.createElement('i');
  close.className = 'ttour-close themetweaker-close-icon';
  $(wrapper).find('.ttour-tip').get(0).appendChild(close);
  $(close).click(function() {
    tourClose(wrapper);
  });
}

function tourClose(el) {
  $('.themetweaker-panel').removeClass('ttour-active ttour-inline');
  $(el).remove();
  switchScroll(false);

  var data = {};
  data[xliteConfig.form_id_name] = xliteConfig.form_id;
  return core.post(
    {
      base: xliteConfig.admin_script,
      target: 'theme_tweaker',
      action: 'tour_shown'
    },
    null,
    data
  );
}

function switchScroll(disable) {
  $('body').css({
    'overflow-y': disable ? 'hidden' : '',
    'margin-right': disable ? window.innerWidth - document.body.clientWidth + 'px' : '',
  });
}

core.bind('themetweaker-panel.activate', function() {
  core.autoload(openTour);
});
