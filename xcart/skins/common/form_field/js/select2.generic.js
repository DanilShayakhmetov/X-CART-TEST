/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Select 2
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('form_field/select2-generic', [], function() {
  return function () {
    var drawTooltipOnLongTitles = function(elements) {
      elements
        .filter(function() {
          return $(this).attr('title').length > 40
        })
        .tooltip({
          placement: 'auto bottom'
        });
    }

    var params = core.getCommentedData($(this));
    var element = $(this);

    $(this).select2({
      debug: core.isDeveloperMode,
      language: {
        noResults: function () {
          return params['no-results-lbl'];
        },
        searching: function () {
          return params['searching-lbl'];
        }
      },
      placeholder: {
        id: "0",
        text: params['placeholder-lbl']
      },
      escapeMarkup: function (markup) { return markup; },
      templateResult: function (state) {
        if (typeof state.text === "undefined") {
          return null;
        }

        var term = $('.select2-search__field', element.parent()).val();

        var text = core.utils.escapeString(state.text);

        return state.loading || !term
          ? text
          : text.replace(new RegExp('('+term+')([^/]*)$', 'i'), '<em>$1</em>$2');
      },
      matcher: function (params, match) {
        if (params.term == null || $.trim(params.term) === '') {
          return match;
        }

        var re = new RegExp('('+params.term+')([^/]*)$', 'i');
        if (re.test(match.text)) {
          return match;
        }

        return null;
      }
    }).on('change', function () {
      var selectedOptions = element.parent().find('.select2-selection__choice');
      drawTooltipOnLongTitles(selectedOptions);
    });

    var selectedOptions = element.parent().find('.select2-selection__choice');
    drawTooltipOnLongTitles(selectedOptions);
  }
})