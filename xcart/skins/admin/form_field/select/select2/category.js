/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-category-select2 select',
    handler: function () {
      var markInaccessibleOptions = function(elements, rootSelect) {
        elements.each(function() {
          var text = $(this).attr('title') || $(this).attr('data-original-title');
          var option = rootSelect.find('option:contains("' + text + '")');

          if (option.length > 0 && option.data('disabled')) {
            $(this).attr('data-disabled', true);
          }
        })
      };

      var addDisabledCategoryTooltip = function(optionText, tooltipLbl, element, rootSelect, enabled) {
        var option = rootSelect.find('option:contains("' + optionText + '")');

        if ((option.length > 0 && option.data('disabled')) || !enabled) {
          element
            .tooltip({
              title: tooltipLbl,
              html: true
            })
            .attr('data-disabled', true);
        }
      };

      var params = core.getCommentedData($(this));
      var element = $(this);

      $(this).select2({
        debug: core.isDeveloperMode,
        language: {
          noResults: function () {
            return params['no-results-lbl'];
          },
          searching: function () {
            return '<span class="searching">' + params['searching-lbl'] + '</span>';
          },
          inputTooShort:function () {
            return params['short-lbl'];
          },
          loadingMore:function () {
            return '<span class="loading-more">' + params['more-lbl'] + '</span>';
          }
        },
        minimumInputLength: 3,
        dropdownParent: element.closest('form'),
        placeholder: params['placeholder-lbl'],
        ajax: {
          url: xliteConfig.script + "?target=search_categories" +
            "&displayNoCategory=" + params.displayNoCategory +
            "&displayRootCategory=" + params.displayRootCategory +
            "&displayAnyCategory=" + params.displayAnyCategory +
            "&excludeCategory=" + params.excludeCategory,
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
        escapeMarkup: function (markup) { return markup; },
        templateResult: function (category, selectItem) {
          if (category.loading) {
            return '<span class="searching">' + params['searching-lbl'] + '</span>';
          }

          var parts = category.path.split('/').map(function (item) {
            return core.utils.escapeString(item);
          });

          var markup = '';
          var additionalClass = '';

          if (category.id == 0) {
            additionalClass = 'any-category';
          }

          if (parts.length > 1) {
            markup += '<span class="path ' + additionalClass + '">' + parts.slice(0, -1).join(' / ') + ' / </span>';
          }

          var name = core.utils.escapeString(category.name)
          markup += '<span class="name ' + additionalClass + '">' + name + '</span>';

          $(selectItem).data('name', name);

          if (category.enabled == undefined) {
            category.enabled = true;
          }

          addDisabledCategoryTooltip(name, params['disabled-lbl'], $(selectItem), element, category.enabled);

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
          $(selectItem).attr('data-original-title', tooltipText);

          var name = category.name ? core.utils.escapeString(category.name) : parts.pop();

          if (category.enabled == undefined) {
            category.enabled = true;
          }

          addDisabledCategoryTooltip(name, params['disabled-lbl'], $(selectItem), element, category.enabled);
          $('.tooltip').hide();

          return name;
        }
      }).on('change', function () {
        var selectedOptions = element.parent().find('.select2-selection__choice');
        $(selectedOptions).removeAttr('title');
        markInaccessibleOptions(selectedOptions, element);
      });

      var selectedOptions = element.parent().find('.select2-selection__choice');
      $(selectedOptions).removeAttr('title');
    }
  }
);
