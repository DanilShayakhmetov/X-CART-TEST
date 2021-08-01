/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  function drawTooltipOnLongTitles(elements) {
    elements
      .filter(function() {
        return $(this).attr('title').length > 40
      })
      .tooltip({
        placement: 'auto bottom'
      });
  }

  function addDisabledCategoryTooltip(optionText, tooltipLbl, element, rootSelect, enabled) {
    var option = $(rootSelect[0].el)
      .find('option')
      .filter(function(index, element) {
        return element.text.endsWith(optionText);
      });

    if ((option.length > 0 && option.data('disabled')) || !enabled) {
      element
        .tooltip({
          title: tooltipLbl,
          html: true
        })
        .attr('data-disabled', true);
    }
  }

  function markInaccessibleOptions(elements, rootSelect) {
    elements.each(function() {
      var text = $(this).attr('title') || $(this).attr('data-original-title');
      var option = $(rootSelect[0].el)
        .find('option')
        .filter(function(index, element) {
          return element.text.endsWith(text);
        });

      if (option.length > 0 && option.data('disabled')) {
        $(this).attr('data-disabled', true);
      }
    })
  }

  Vue.directive('xliteProductCategory', {
    params: ['searchingLbl', 'noResultsLbl', 'enterTermLbl', 'placeholderLbl', 'disabledLbl', 'shortLbl', 'moreLbl'],
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
            url: xliteConfig.script + "?target=search_categories",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              var query = {
                search: params.term,
                page: params.page || 1
              };

              return query;
            },
            processResults: function (data, params) {
              params.page = params.page || 1;

              return {
                results: data.categories,
                pagination: {
                  more: data.more
                }
              };
            },
          },
          templateResult: function (category, selectItem) {
            if (category.loading) {
              return '<span class="searching">' + self.params.searchingLbl + '</span>';
            }

            var parts = category.path.split('/').map(function (item) {
              return core.utils.escapeString(item);
            });

            var markup = '';
            if (parts.length > 1) {
              markup += '<span class="path">' + parts.slice(0, -1).join(' / ') + ' / </span>';
            }

            var name = core.utils.escapeString(category.name)
            markup += '<span class="name">' + name + '</span>';

            $(selectItem).data('name', name);

            if (category.enabled == undefined) {
              category.enabled = true;
            }

            addDisabledCategoryTooltip(name, self.params.disabledLbl, $(selectItem), $(self), category.enabled);

            return markup;
          },
          templateSelection: function (category, selectItem) {
            var path = category.path == undefined ? category.text : category.path;
            var parts = path.split('/').map(function (item) {
              return core.utils.escapeString(item);
            });

            var tooltipText = '<div class="path">';
            parts.forEach(function(part) {
              tooltipText += '<div class="part">' + part;
            });
            parts.forEach(function() {
              tooltipText += '</div>';
            });
            tooltipText += '</div>';

            $(selectItem).tooltip({
              title: tooltipText,
              html: true,
              placement: 'auto bottom'
            });

            var name = category.name ? core.utils.escapeString(category.name) : parts.pop();

            if (category.enabled == undefined) {
              category.enabled = true;
            }

            addDisabledCategoryTooltip(name, self.params.disabledLbl, $(selectItem), $(self), category.enabled);
            $('.tooltip').hide();

            return name;
          }
        })
        .on('select2:select', _.bind(function () {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this))
        .on('select2:unselect', _.bind(function () {
          var $el = $(this.el);
          this.vm.$set(model, $el.val() || []);
        }, this))
        .on('change', function () {
          var selectedOptions = $el.parent().find('.select2-selection__choice');
          markInaccessibleOptions(selectedOptions, $el);
          drawTooltipOnLongTitles(selectedOptions);
        });

      var selectedOptions = $el.parent().find('.select2-selection__choice');
      markInaccessibleOptions(selectedOptions, $el);
      drawTooltipOnLongTitles(selectedOptions);

      this.vm.$watch(this.expression, function (newValue) {
        self.vm.$set('form.default.category_tree', newValue);
      });

      this.vm.$watch('form.default.category_tree', function (newValue) {
        self.vm.$set(self.expression, newValue);
      });

      jQuery(this.el).select2Sortable(function() {
        var ul = $el.next('.select2-container')
          .first('ul.select2-selection__rendered');

        var reservedChoices = jQuery(ul).find('.select2-selection__choice').get().reverse();

        jQuery(reservedChoices).each(function() {
          var id = $(this).data('data').id;
          var option = $this.find('option[value="' + id + '"]')[0];
          $this.prepend(option);
        });

        self.vm.$set('form.default.category_tree', $el.val());
      });

      setTimeout(function () {
        $el.closest('.input-widget').find('span.help-block a').click(function () {
          self.vm.$set('form.default.category_widget_type', 'tree');
          jQuery.cookie('product_modify_categroy_widget', 'tree');
        });

        $('#form_default_category_tree').attr('name', '').closest('.input-widget').find('span.help-block a').click(function () {
          self.vm.$set('form.default.category_widget_type', 'search');
          jQuery.cookie('product_modify_categroy_widget', 'search');
        });
      }, 1000);
    },
    update: function (newValue) {
      if (newValue.filter(function (value) { return value.length }).length === 0) {
        return;
      }

      var $el = $(this.el);

      $el.val(newValue);
      $el.trigger('change');
    }
  });

})();
