/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteTags', {
    params: ['searchingLbl', 'noResultsLbl', 'allowCreateTags'],
    twoWay: true,
    bind: function () {
      var self = this;
      var $el = $(this.el);
      var model = this.expression;

      $el
        .select2(
          {
            language: {
              noResults: function () {
                return self.params.noResultsLbl;
              },
              searching: function () {
                return self.params.searchingLbl;
              }
            },
            tags: self.params.allowCreateTags,
            escapeMarkup: function (markup) {
              return markup;
            },
            createTag: function (params) {
              return {
                id: '_' + params.term,
                text:  params.term,
                newTag: true // add additional parameters
              }
            }
          }
        )
        .on('select2:select', _.bind(function (e) {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this))
        .on('select2:unselect', _.bind(function (e) {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this));
    }
  });

})();
