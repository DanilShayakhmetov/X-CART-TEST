/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('datepicker', {
    twoWay: true,
    params: ['format', 'firstday', 'locale'],
    bind: function () {
      var el = this.el;
      var $el = $(el);
      var vm = this.vm;
      var model = this.expression;
      var format = this.params.format;
      var defaultDate = $el.val();
      var locale = this.params.locale;

      var changeHiddenValue = function ($el) {
        var selectedDate = $.datepicker.formatDate($($el).datepicker('option', 'dateFormat'), $($el).datepicker('getDate'), $.datepicker.regional['']);
        $($el).siblings('.datepicker-value-input').val(selectedDate);
      };

      $.datepicker.setDefaults($.datepicker.regional['']);
      if ($.datepicker.regional[locale] !== undefined) {
        $.datepicker.setDefaults($.datepicker.regional[locale]);

        if (defaultDate !== undefined && defaultDate !== '') {
          defaultDate = $.datepicker.parseDate(format, defaultDate, $.datepicker.regional['']);
        }
      }

      $el.datepicker({
        dateFormat: format,
        defaultDate: defaultDate,
        firstDay:   parseInt(this.params.firstday),
        onSelect: function (date) {
          changeHiddenValue(this);

          vm.$set(model, '' + $(this).datepicker('getDate'));

          // DateRange validator is detached from change/blur field event
          // so trigger validation
          vm.$validate(true);
        }
      });

      $el.change(function(){
        changeHiddenValue(this);

        vm.$set(model, '' + $(this).datepicker('getDate'));

        // DateRange validator is detached from change/blur field event
        // so trigger validation
        vm.$validate(true);
      });

      $el.blur(function () {
        var result = null;
        try {
          result = $.datepicker.parseDate(format, $el.val());
        } catch(err) {
          result = false;
        }

        if (!result) {
          $el.datepicker('setDate', defaultDate);
          $el.datepicker('refresh');

          changeHiddenValue($el);
        }
      });

      $el.datepicker('setDate', defaultDate);
      $el.datepicker('refresh');

      changeHiddenValue($el);
    }
  });

})();
