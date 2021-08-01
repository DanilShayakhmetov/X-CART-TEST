/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Remove button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function attribute_makeLineBlank(value) {
  var $value = jQuery(value);
  var input = $value.find(':input');
  var $line = $value.closest('.attribute-value .line');

  if ($line.length) {
    var removeButton = $line.find('.actions button.remove');

    if ($line.is(':last-child') || $line.is('.remove-mark')) {
      return;
    }

    removeButton.click();
  }

  if ((!$line.length && $value.is(':first-child')) || ($line.length && $line.is(':first-child'))) {
    input.data('makeblank-dump-val', input.val());
    input.val('');
    $value.closest('form').change();
  }
}

function attribute_revertBlankLine(value) {
  var $value = jQuery(value);
  var input = $value.find(':input');
  var $line = $value.closest('.attribute-value .line');

  if ($line.length) {
    var removeButton = $line.find('.actions button.remove');

    if ($line.is(':last-child') || !$line.is('.remove-mark')) {
      return;
    }
    removeButton.click();
  }

  if ((!$line.length && $value.is(':first-child')) || ($line.length && $line.is(':first-child'))) {
    input.val(input.data('makeblank-dump-val'));
  }
}

CommonForm.elementControllers.push(
  {
    pattern: '.lines .line .actions .makeblank-wrapper',
    handler: function () {
      var wrapper = jQuery(this);

      wrapper.startTooltip();

      wrapper.find('button').click(function(event) {
        var button = jQuery(this);
        var values = button.parents('.line').find('.attribute-value .table-value');

        if (button.data('blank')) {
          _.each(values, attribute_revertBlankLine);
          button.data('blank', false);
        } else {
          _.each(values, attribute_makeLineBlank);
          button.data('blank', true);
        }

        event.preventDefault();
        event.stopPropagation();

        return false;
      });
    }
  }
);
