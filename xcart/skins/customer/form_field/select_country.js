/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Country selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StateSelector(countrySelectorId, stateSelectorId, stateInputId) {
  this.countrySelectBox = jQuery('#' + countrySelectorId);
  this.stateSelectBox = jQuery('#' + stateSelectorId);
  this.stateInputBox = jQuery('#' + stateInputId);

  if (this.stateSelectBox.length == 0) {
    return;
  }

  if (this.stateSelectBox.val()) {
    this.stateSelectBoxValue = this.stateSelectBox.val();

  } else if (this.stateSelectBox.attr('data-value')) {
    this.stateSelectBoxValue = this.stateSelectBox.attr('data-value');
  }

  this.stateInputBoxValue = this.stateInputBox.val();

  // Event handlers
  var o = this;

  this.countrySelectBox.change(
    function (event) {
      return o.changeCountry(this.value);
    }
  );

  this.stateSelectBox.change(
    function (event) {
      return o.changeState(this.value);
    }
  );

  this.stateSelectBox.getParentBlock = function () {
    return o.getParentBlock(this);
  };

  this.stateInputBox.getParentBlock = function () {
    return o.getParentBlock(this);
  };

  this.countrySelectBox.change();

  // Re-initialize state selector's CommonElement object initial value
  var elm = new CommonElement(this.stateSelectBox[0]);
  elm.bindElement(this.stateSelectBox[0]);
  elm.saveValue();
}

// used in vue.js components to track change (because Vue doesn't listens for JQuery change)
StateSelector.sendNativeChangeOnAddStates = false;

StateSelector.prototype.countrySelectBox = null;
StateSelector.prototype.stateSelectBox = null;
StateSelector.prototype.stateInputBox = null;
StateSelector.prototype.stateSavedValue = null;

StateSelector.prototype.getParentBlock = function (selector) {
  var block = selector.closest('li');

  if (!block.length) {
    block = selector.closest('div');
  }

  return block;
};

StateSelector.prototype.changeCountry = function (country) {
  if (this.getParentBlock(this.countrySelectBox).length) {
    if (StatesList.getInstance().getStates(country)) {

      if (StatesList.getInstance().isForceCustomState(country)) {
        this.stateSelectBox.getParentBlock().hide();
        this.stateInputBox.getParentBlock().show();

        $(this.stateInputBox).autocomplete({
          source: StatesList.getInstance().getStatesArray(country),
          minLength: 1
        }).addClass('validate[required]')
          .parents('.table-value:first')
          .addClass('table-value-required')
          .prev('.star')
          .text('*')
          .prev('.table-label')
          .addClass('table-label-required');
      } else {
        this.removeOptions();
        this.addStates(StatesList.getInstance().getStates(country));

        this.stateSelectBox.getParentBlock().show();
        this.stateInputBox.getParentBlock().hide();
      }

    } else {
      this.stateSelectBox.getParentBlock().hide();
      this.stateInputBox.getParentBlock().show();

      $(this.stateInputBox)
        .autocomplete()
        .autocomplete("destroy")
        .removeClass('validate[required]')
        .parents('.table-value:first')
        .removeClass('table-value-required')
        .prev('.star')
        .html('&nbsp;')
        .prev('.table-label')
        .removeClass('table-label-required');
    }
  }
};

StateSelector.prototype.changeState = function (state) {
};

StateSelector.prototype.removeOptions = function () {
  var s = this.stateSelectBox.get(0);

  if (this.stateSelectBox.val()) {
    this.stateSavedValue = this.stateSelectBox.val();

  } else {
    this.stateSavedValue = this.stateSelectBoxValue;
  }

  if (s) {
    jQuery(s).find('optgroup').remove();
    for (var i = s.options.length - 1; i >= 0; i--) {
      s.options[i] = null;
    }
  }
};

StateSelector.prototype.addDefaultOptions = function () {
  this.stateSelectBox.get(0).options[0] = new Option(core.t('Select one'), '');
};

StateSelector.prototype.addStates = function (states) {
  this.addDefaultOptions();

  var s = this.stateSelectBox.get(0);
  if (s) {
    var added = s.options.length;
    var i = 0;

    if (states) {
      for (var id in states) {
        if (!states[id].label) {
          s.options[i + added] = new Option(states[id].name, states[id].key);
        } else {
          var optgroupValues = states[id];
          var optgroup = jQuery('<optgroup/>');
          optgroup.attr('label', optgroupValues['label']);
          for (var i = 0; i < optgroupValues['options'].length; i++) {
            optgroup.append('<option value="' + optgroupValues['options'][i].key + '">' + optgroupValues['options'][i].name + '</option>');
          }
          jQuery(s).append(optgroup)
        }
        i++;
      }
    }
  }

  this.stateSelectBox.val(this.stateSavedValue);
  if (!this.stateSelectBox.val() && this.stateSelectBox.get(0).options.length > 0) {
    this.stateSelectBox.val(this.stateSelectBox.get(0).options[0].value);
  }

  if (StateSelector.updateStateValueOnce) {
    this.stateSelectBox.get(0).fireEvent('change');
    StateSelector.updateStateValueOnce = false;
  }
};

jQuery(document).ready(function() {
    StatesList.getInstance();
});
