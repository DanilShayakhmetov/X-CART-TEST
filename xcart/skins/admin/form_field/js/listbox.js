/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Listbox javascript functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind('load', function (event) {
  var listboxes = jQuery('.input-listbox .listbox');

  listboxes.each(function () {
    var from = $(this).find('.listbox-item.from select'),
      to = $(this).find('.listbox-item.to select'),
      add = $(this).find('.listbox-actions button.add'),
      remove = $(this).find('.listbox-actions button.remove'),
      input = $(this).find('> input[type=hidden]');

    var listbox = this;

    this.renewListboxValue = function () {
      var separator = input.data('separator');

      var result = [];

      to.find('option').each(function () {
        result.push($(this).val());
      });

      input.val(result.join(separator)).change();
      from.trigger('change');
      to.trigger('change');

      this.sortListboxOptions();
    };

    this.selectListboxOption = function (option) {
      to.append(option);
      this.renewListboxValue();
      to.change();
      from.change();
    };

    this.deselectListboxOption = function (option) {
      from.append(option);
      this.renewListboxValue();
    };

    this.moveListboxOption = function (option) {
      if (to.has(option).length) {
        this.deselectListboxOption(option);
      } else if (from.has(option).length) {
        this.selectListboxOption(option);
      }
    };

    this.sortListboxOptions = function () {
      var options = from.find('option').sort(function(a, b) { return $(a).text() > $(b).text() ? 1 : -1; });
      from.append(options);

      options = to.find('option').sort(function(a, b) { return $(a).text() > $(b).text() ? 1 : -1; });
      to.append(options);
    };

    if (from.length && to.length && input.length) {
      from.find('option').each(function () {
        $(this).dblclick(function () {
          listbox.moveListboxOption(this);
        });
      });

      to.find('option').each(function () {
        $(this).dblclick(function () {
          listbox.moveListboxOption(this);
        });
      });

      add.click(function () {
        listbox.selectListboxOption(from.find('option:selected'));
      });

      remove.click(function () {
        listbox.deselectListboxOption(to.find('option:selected'));
      });
    }
  });
});
