/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common items list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Main class
function ItemsListPayment(elem)
{
  this.base = elem;

  this.addListeners();
}

ItemsListPayment.prototype.addListeners = function ()
{
  var base = this.base;

  jQuery('.switcher input[type=checkbox]', this.base).change(function (event)
  {
    event.stopImmediatePropagation();

    core.trigger('payment.methods.switch', jQuery(this));

    return false;
  });

  jQuery('.remove a', this.base).click(function (event)
  {
    event.stopImmediatePropagation();

    if (!confirm(core.t('Are you sure you want to delete the selected payment method?'))) {
      return false;
    }

    core.trigger(
      'payment.methods.remove',
      {
        href: jQuery(this).attr('href'),
        line: jQuery(this).closest('.cell'),
        base: base
      }
    );

    return false;
  });

  jQuery('.switcher', this.base).click(
    function () {
      var p = jQuery(this).parent().parent();
      if (p.hasClass('blocked-enable')) {
        p.find('button.configure').addClass('hover');
        setTimeout(function(){
          p.find('button.configure').removeClass('hover');
        }, 1000);
      }
    }
  );
};

// Payment methods switch event
core.bind(
  'payment.methods.switch',
  function (event, data)
  {
    var $switch = data;
    var methodId = $switch.data('methodId');

    core.get(
      window.URLHandler.buildURL({target: "payment_settings", action: 'switch', id: methodId}),
      function (data) {
        core.trigger('payment.methods.switch.loaded', {data: data, switcher: $switch});
      }
    ).fail(function() {
      // toggle back on failed request
      $switch.prop('checked', !$switch.prop('checked'));
    })
  }
);

// Payment methods remove event
core.bind(
  'payment.methods.remove',
  function (event, data)
  {
    var line = data.line;
    var base = data.base;
    var href = data.href;

    core.get(
      href,
      function () {
        core.trigger('payment.methods.remove.loaded', {line: line, base: base});
      }
    );
  }
);

// Payment methods remove loaded event
core.bind(
  'payment.methods.remove.loaded',
  function (event, data)
  {
    var moduleName = data.line.data('module-name');

    core.trigger('payment.methods.remove.loaded.started', data);
    event.stopImmediatePropagation();

    if (typeof data.line.donotRemove === 'undefined') {
      if (data.line.hasClass('has-icon')) {
        jQuery('.line-row, button', data.line).remove();
      } else {
        data.line.remove();
      }

      if (moduleName && jQuery('[data-module-name="' + moduleName + '"] .line-row', data.base).length === 0) {
        jQuery('[data-module-name="' + moduleName + '"]', data.base).remove();
      }
    }
  }
);

core.microhandlers.add(
  'ItemsListPaymentQueue',
  '.items-list.methods',
  function (event) {
    jQuery(this).each(function (index, elem) {
      new ItemsListPayment(jQuery(elem));
    });
  }
);
