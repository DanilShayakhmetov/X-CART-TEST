/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


define('modules/XC/FroalaEditor/colorPicker', function () {

  return Object.extend({
    constructor: function ColorPicker(element) {
      this.el = element;
      this.$el = jQuery(this.el);

      this.changeHandler = _.throttle(this.changeValue, 50);
    },

    start: function() {
      this.$el.ColorPicker(this.getOptions());

      jQuery(document).keypress(function(e){
        var colorPicker = jQuery('.colorpicker .colorpicker_submit')
        if (e.which == 13 && colorPicker.is(':visible') && colorPicker.length) {
          colorPicker.click();
        }
      });
    },

    changeValue: function (owner, hex) {
      var input = owner;
      if (owner.find('.color-picker-value').length) {
        input = owner.find('.color-picker-value');
      }

      input.val(hex);
      input.change();
    },

    getOptions: function() {
      var self = this;

      return {
        onShow: function (colpkr) {
          self.getOwner().data('colorpicker-show', true);
          self.getOwner().ColorPickerSetColor(this.value);
          jQuery(colpkr).fadeIn(250);

          return false;
        },
        onHide: function (colpkr) {
          self.getOwner().data('colorpicker-show', false);
          self.getOwner().blur();

          jQuery(colpkr).fadeOut(250);

          return false;
        },
        onSubmit: function(hsb, hex, rgb, el) {
          self.getOwner().ColorPickerHide();
          self.changeHandler(self.getOwner(), hex);
          core.trigger('color-palette.submit-color');
        },
        onChange: function(hsb, hex, rgb, el) {
          self.changeHandler(self.getOwner(), hex);
        },
        onBeforeShow: function () {
          self.setOwner(jQuery(this));

          self.$el.ColorPickerSetColor(this.value);
        }
      };
    },

    getOwner: function () {
      return jQuery('.colorpicker').get(0).owner;
    },

    setOwner: function (owner) {
      jQuery('.colorpicker').get(0).owner = owner;
    },

  });
});
