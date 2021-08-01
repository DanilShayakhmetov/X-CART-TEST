/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Inline form field common controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.edit-on-click-field',
    handler: function () {
      
      // Field properties and methods
      var field = jQuery(this);
      var obj = this;

      this.viewValuePattern = '.view';

      var line = field.parents('.line').eq(0);
      var list = line.parents('.items-list').eq(0);
      var row = line.get(0);
      var inputs = jQuery('.field :input', this);

      var vTab = !!list.data('vtab');

      this.startEdit = function() {
        if (
          field.hasClass('editable')
          && (!field.parents('.line').length || !field.parents('.line').hasClass('remove-mark'))
        ) {
          field.trigger('beforeStartEditEditOnCLick');

          if (row) {
            line.addClass('edit-open-mark');

          } else {
            field.addClass('edit-open-mark');
          }

          jQuery('.field :input', this).first().focus();
          field.trigger('startEditEditOnCLick');
        }
      }

      // View click effect (show field and hide view)
      jQuery('.view', this).click(_.bind(this.startEdit, this));

      this.getViewValueElements = function()
      {
        return field.find(this.viewValuePattern);
      }

      // Save field into view
      this.saveField = function()
      {
        var value = this.getFieldFormattedValue();

        // undefined value cannot be saved
        if (value !== undefined && "" !== value) {
          var preparedValue = field.data('is-escape')
            ? htmlspecialchars("" == value ? " " : value, null, null, false)
            : ("" == value ? " " : value);
          var data = {
            'value': preparedValue
          };
          field.trigger('beforeSaveFieldEditOnCLick', data);
          this.getViewValueElements().html(data.value);
          field.trigger('afterSaveFieldEditOnCLick', data);

        } else {
          field.trigger('saveEmptyFieldEditOnCLick');
        }
      };

      this.endEdit = function(noSave)
      {
        if (field.hasClass('edit-open-mark')) {
          if (!noSave) {
            this.saveField();
          }

          if (inputs.get(0).value === inputs.get(0).initialValue) {
            field.removeClass('edit-open-mark');
          }
          field.trigger('endEditEditOnCLick');
        }
      }

      // Get field(s) formatted value (usage as view content)
      this.getFieldFormattedValue = function(input)
      {
        input = input ? jQuery(input).eq(0) : inputs.eq(0);
        var result = '';
        if (input) {
          if (input.is('select')) {
            var elm = input.get(0);
            var option = jQuery(elm.options[elm.selectedIndex]);
            if (option.data('value')) {
              result = option.data('value');

            } else {
              result = elm.options[elm.selectedIndex].text;
            }

          } else {
            result = input.val();
          }
        }

        return result;
      };

      // Sanitize-and-set value into field
      this.sanitize = function()
      {
      };

      // Check - process blur event or not
      this.isProcessBlur = function()
      {
        return true;
      };

      // Field input(s)

      inputs.bind(
        'undo',
        function () {
          field.get(0).saveField();
        }
      );

      // Input blur effect (initialize save fields group)
      inputs.blur(
        function () {
          var result = true;

          if (obj.isProcessBlur()) {
            obj.sanitize();
            result = !jQuery(this).validationEngine('validate');

            if (result && row) {
              row.editOnClickGroupBlurTimeout = setTimeout(
                function () {
                  row.editOnClickGroupBlurTimeout = false;
                  row.saveFields();
                },
                100
              );
            }

            obj.endEdit();
          }

          return result;
        }
      );

      // Cancel save fields group if focus move to input in this group
      inputs.focus(
        function () {
          if (row && row.editOnClickGroupBlurTimeout) {
            clearTimeout(row.editOnClickGroupBlurTimeout);
            row.editOnClickGroupBlurTimeout = false;
          }
        }
      );

      // Move focus to next field in this column (if axists)
      inputs.keydown(
        function (event) {
          var result = {state: true};

          // Press 'Tab' / 'Enter' button
          if (!vTab && (9 === event.keyCode || 13 === event.keyCode) && !$(this).is('textarea')) {
            if (!$(this).is('textarea')) {
              $(this).trigger('enterPress', [event, result]);
            } else if (event.metaKey || event.ctrlKey) {
              $(this).trigger('enterPress', [event, result]);
            }
          } else if (27 === event.keyCode) {
            $(this).trigger('escPress', [event, result]);
          }

          return result.state;
        }
      );

      inputs.bind(
        'escPress',
        function (currentEvent, event, result) {
          inputs.each(function () {
            var input = $(this);

            if (input.get(0).commonController) {
              input.val(input.get(0).commonController.element.initialValue);
              input.change();
            }
          });

          jQuery(this).trigger('blur');

          result.state = false;
        }
      );

      // Line (fields group) methods

      if (row && typeof(row.saveFields) == 'undefined') {

        row.editOnClickGroupBlurTimeout = false;

        // Save line fields into views
        row.saveFields = function()
        {
          jQuery('.inline-field', this).each(
            function () {
              this.saveField();
              jQuery(this).removeClass('edit-mark');
            }
          );
          jQuery(this).removeClass('edit-open-mark');
        };

        // Add line hover effect
        line.hover(
          function() {
            jQuery(this).addClass('edit-mark');
          },
          function() {
            jQuery(this).removeClass('edit-mark');
          }
        );
      }

    }
  }
);
