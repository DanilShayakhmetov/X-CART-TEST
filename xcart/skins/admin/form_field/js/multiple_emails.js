/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Multiple emails controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    canApply: function () {
      return 0 < this.$element.filter('select.multiple-emails').length;
    },
    handler: function () {
      var element = this.$element;
      element.css({display: 'none'});
      element.data('jqv', {validateNonVisibleFields: true});

      var isDisabled = Boolean(element.attr('disabled'));

      var container = $('<div class="multiple-emails-container form-control" tabindex="-1"><div class="options"></div></div>').insertAfter(element);

      if (isDisabled) {
        container.addClass('disabled');
      }

      function getOptionTemplate() {
        return '<div class="option"><div class="option-text"></div><div class="option-remove"><span>×</span></div></div>';
      }

      function getOptionElem(initialOption) {
        var option = $(getOptionTemplate());
        option.data('option', initialOption);
        option.find('.option-text').text(initialOption.text());

        if (!isDisabled) {
          option.find('.option-remove').click(function () {
            option.data('option').remove();
            option.remove();
            element.change();
          });
        }

        return option;
      }

      element.find('option').each(function () {
        container.find('.options').append(getOptionElem($(this)));
      });

      container.bind('try2CreateOption', function () {
        var input = $(this).find('[contenteditable="true"]').first();
        var val = input.text();

        var duplicates = element.find('option').filter(function () {
          return $(this).val() === val;
        }).length;

        if (
          val
          && val.length
          && val.match(/.+@.+\..+/)
          && !val.match(/\.\./)
          && !duplicates
        ) {
          var option = $('<option>', {
            value: input.text(),
            text: input.text(),
            selected: true
          }).appendTo(element);

          getOptionElem(option).insertBefore(input);
          input.text('');
          container.removeClass('has-error');
          element.change();
        } else if (val && val.trim().length) {
          container.addClass('has-error');
          input.focus();
        } else {
          container.removeClass('has-error');
        }
      });

      function getInputElem() {
        var input = $('<span contenteditable="true"></span>');
        input.on('keypress', function (evt) {
          if ([13, 32, 44].indexOf(evt.which) !== -1) {
            container.trigger('try2CreateOption');
            return false;
          }
        }).focus(function () {
          container.addClass('focused');
        }).blur(function (e) {
          if (!e.relatedTarget || e.relatedTarget !== this || e.relatedTarget !== container.get(0)) {
            container.removeClass('focused');
            container.trigger('try2CreateOption');
          }
        });
        return input;
      }

      if (!isDisabled) {
        container.find('.options').append(getInputElem());
        container.click(function (e) {
          if ($(e.target).has('.multiple-emails-container')) {
            $(this).find('[contenteditable="true"]').focus();
          }
        });
        container.find('.options').sortable({
          items: ".option",
          update: function (event, ui) {
            var elem = $(ui.item.get(0));

            if (elem.next().length && elem.next().is('.option')) {
              elem.data('option').insertBefore(elem.next().data('option'));
            } else if (elem.prev().length) {
              elem.data('option').insertAfter(elem.prev().data('option'));
            }

            element.change();
          }
        });
        element.get(0).commonController.isChanged = function () {
          return !this.isEqualArrayValues(this.element.initialValue, this.$element.val(), this.$element);
        }
      }
    }
  }
);
