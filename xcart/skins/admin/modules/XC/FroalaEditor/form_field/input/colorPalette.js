/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


define('modules/XC/FroalaEditor/colorPalette',
    ['js/vue/vue', 'modules/XC/FroalaEditor/colorPicker'],
    function (XLiteVue, ColorPicker) {

  XLiteVue.component('color-palette-picker', {

    replace:false,

    data: function() {
      return {
        colors:     [],
        colorToAdd: '#cccccc',
      };
    },

    ready: function() {
      CommonForm.autoassign(jQuery(this.$el));

      var addButton = jQuery(this.$el).find('.new-color');

      this.initColorpicker(addButton);

      var self = this;
      core.bind('color-palette.submit-color', function() {
        self.addColor()
      });
    },

    computed: {
      colorPalette: function() {
        var colors = _.map(
            this.colors,
            function(color) {
              return color.value;
            }
        );
        return colors.join(',');
      },
    },

    watch: {
      colorPalette: function(value, oldValue) {
        jQuery(this.$el).find(':input').change();
      }
    },

    methods: {
      addColor: function() {
        var color = this.colorToAdd.replace(/^#/,"");

        this.colors.push({ value: color });
      },

      removeColor: function(index) {
        this.colors.splice(index, 1);
      },

      initColorpicker: function(element) {
        this.colorpicker = new ColorPicker(element);
        this.colorpicker.start();
      },
    },

  });

});
