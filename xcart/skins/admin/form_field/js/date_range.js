/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Date range field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return this.$element.is('input.date-range');
    },
    handler: function() {
      const config = this.$element.data('datarangeconfig');
      $.datepicker.setDefaults($.datepicker.regional[config.language]);

      this.$element.daterangepicker({
        presetRanges: [{
          text: config.labels.today,
          dateStart: function() {
            return moment();
          },
          dateEnd: function() {
            return moment();
          }
        }, {
          text: config.labels.thisWeek,
          dateStart: function() {
            return moment().startOf('week');
          },
          dateEnd: function() {
            return (moment().endOf('week') > moment()) ? moment() : moment().endOf('week');
          }
        }, {
          text: config.labels.thisMonth,
          dateStart: function() {
            return moment().startOf('month');
          },
          dateEnd: function() {
            return (moment().endOf('month') > moment()) ? moment() : moment().endOf('month');
          }
        }, {
          text: config.labels.thisQuarter,
          dateStart: function() {
            return moment().startOf('quarter');
          },
          dateEnd: function() {
            return (moment().endOf('quarter') > moment()) ? moment() : moment().endOf('quarter');
          }
        }, {
          text: config.labels.thisYear,
          dateStart: function() {
            return moment().startOf('year');
          },
          dateEnd: function() {
            return (moment().endOf('year') > moment()) ? moment() : moment().endOf('year');
          }
        }, {
          text: config.labels.allTime,
          dateStart: function() {
            return null;
          },
          dateEnd: function() {
            return null;
          }
        }],
        applyOnMenuSelect: false,
        dateFormat: config.format,
        rangeSplitter: config.separator,
        mirrorOnCollision: false,
        datepickerOptions: {
          numberOfMonths: 2,
        }
      });
    }
  }
);