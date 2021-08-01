/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function setFormAttribute(form, name, value)
{
  form.elements[name].value = value;
}

function setFormAction(form, action)
{
  setFormAttribute('action', action);
}

function disableInputsInForm(form) {
  var inputs = jQuery(form).find(':input');

  if (inputs.length) {
    _.each(inputs, function(input) {
      $(input).attr('disabled', true);
    });
  }

}

function submitForm(form, attrs)
{
  jQuery.each(
    attrs,
    function (name, value) {
      var e = form.elements.namedItem(name);
      if (e) {
        e.value = value;
      }
    }
  );
  var disableInputs = _.partial(disableInputsInForm, form);

  if (form.commonController) {
    var valid = form.commonController.validate({silent: true});

    if (!valid) {
      disableInputs = function () {};
    }
  }

  jQuery(form).submit();
  _.delay(disableInputs, 100);
}

function submitFormDefault(form, action)
{
  var attrs = {};

  if (action !== null) {
  	attrs['action'] = action;
  }

  submitForm(form, attrs);
}
