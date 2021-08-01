/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var box = {}

// Shade block with content
function shadeBlock () {
  if (0 !== jQuery(box).length) {
    var overlay = jQuery(document.createElement('div'))
      .addClass('single-progress-mark')
    jQuery(document.createElement('div'))
      .appendTo(overlay)

    overlay.width(box.outerWidth())
      .height(box.outerHeight())

    overlay.appendTo(box)
  }
}

function unshadeBlock () {
  if (0 !== jQuery(box).length) {
    jQuery(box).find('.single-progress-mark').remove()

    box = {}
  }
}

function filterByGroup (element, data) {
  if ($(element).length > 0 && $(element).data('input-group')) {
    var group = $(element).data('input-group');
    var current = String($(element).val());
    var selected = [];
    var inputs = $('[data-input-group="' + group + '"]');
    inputs.each(function () {
      selected.push(String($(this).val()));
    });

    return _.filter(data, function (item) {
      return !_.contains(selected, String(item.value)) || String(item.value) === current;
    })
  }

  return data;
}

var suggestions = new window.CacheEngine()

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.auto-complete',
    handler: function () {
      var input = jQuery(this);

      if ('undefined' === typeof this.autocompleteSource) {
        this.autocompleteSource = function (request, response) {
          unshadeBlock()

          box = input.parent('span')

          var url = decodeURIComponent(input.data('source-url')).replace('%term%', request.term)
          if (!suggestions.has(url)) {
            shadeBlock()

            window.core.get(
              url,
              null,
              {},
              {
                dataType: 'json'
              }
            ).then(function (data) {
              suggestions.add(url, data)
              response(filterByGroup(input, data))

              unshadeBlock()
            })
          } else {
            response(filterByGroup(input, suggestions.get(url)))
          }
        }
      }

      if ('undefined' === typeof this.autocompleteAssembleOptions) {
        this.autocompleteAssembleOptions = function () {
          return {
            source: this.autocompleteSource.bind(this),
            minLength: input.data('min-length') || 2,
            close: function () {
              input.keyup()
            },
            select: function () {
              input.dblclick()
            }
          }
        }
      }

      input.autocomplete(this.autocompleteAssembleOptions())
    }
  }
)

