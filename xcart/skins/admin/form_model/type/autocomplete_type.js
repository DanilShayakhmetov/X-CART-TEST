/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteAutocomplete', {
    params: ['searchingLbl', 'noResultsLbl', 'enterTermLbl', 'placeholderLbl', 'shortLbl', 'moreLbl', 'dictionary'],
    twoWay: true,
    bind: function () {
      var self = this;
      var $el = $(this.el);
      var model = this.expression;

      $el
        .select2({
          debug: core.isDeveloperMode,
          language: {
            noResults: function () {
              return self.params.noResultsLbl;
            },
            searching: function () {
              return '<span class="searching">' + self.params.searchingLbl + '</span>';
            },
            inputTooShort:function () {
              return self.params.shortLbl;
            },
            loadingMore:function () {
              return '<span class="loading-more">' + self.params.moreLbl + '</span>';
            }
          },
          minimumInputLength: 3,
          placeholder: this.params.placeholderLbl,
          escapeMarkup: function (markup) { return markup; },
          ajax: {
            url: xliteConfig.script + "?target=autocomplete",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              var query = {
                term: params.term,
                dictionary: self.params.dictionary
              };

              return query;
            },
            processResults: function (data) {
              $.each(data, function (index, value) {
                data[index].id = value.value;
                data[index].text = value.label.name;
              });

              return {
                results: data
              };
            },
          },
          templateResult: function (item, selectItem) {
            if (item.loading) {
              return '<span class="searching">' + self.params.searchingLbl + '</span>';
            }

            var markup = '<span>' + item.label.name + '</span>';

            $(selectItem).data('name', item.label.name);

            return markup;
          },
        })
        .on('select2:select', _.bind(function () {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this))
        .on('select2:unselect', _.bind(function () {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this));

      $el.trigger('select2:select');
    },
    update: function (newValue) {
      var $el = $(this.el);

      $el.val(newValue);
      $el.trigger('change');
    }
  });
})();
