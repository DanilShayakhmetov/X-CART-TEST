/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Attributes
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var idx = 1;

jQuery().ready(
  function () {
    jQuery('.create-tpl button.remove,.new button.remove').click(
      function () {
        var box = jQuery(this).parents('ul').eq(0);
        jQuery(this).parents('li.line').eq(0).remove();
        if (0 == box.find('li.line').length) {
           box.parent().addClass('empty');
        }
      }
    );

    jQuery('.multiple-checkbox input').change(
      function () {
        if (jQuery(this).prop('checked')) {
          jQuery(this).parent().parent().addClass('multiple').find('.values .new input').focus();

        } else {
          jQuery(this).parent().parent().removeClass('multiple').find('.values > li:first-child input').focus();
        }
      }
    );

    jQuery('.modifiers input[type=text]').regexMask(/^[\-\+]{1}([0-9]*)([\.,]([0-9]*))?([%]{1})?$/);

    jQuery('.modifiers a').click(
      function () {
        var p = jQuery(this).parent();
        if (p.hasClass('open')) {
              changeModifiers(p)

        } else {
          jQuery('.modifiers.open').each(
            function () {
              changeModifiers(jQuery(this))
            }
          );
          p.addClass('open');
        }

        return false;
      }
    );

    jQuery('.type-s .values .new').bind(
      'keyup change focusin dblclick',
      function (event) {
        var line = jQuery(this);
        if (
          line.hasClass('new')
          && (
            line.find('input[type=text]').val()
            || (
              event.relatedTarget
              && event.relatedTarget.className
              && event.relatedTarget.className.indexOf('ui-corner-all') >= 0
            )
            || event.type == 'dblclick'
          )
        ) {
          var newLine = line.clone(true);
          newLine.find('input[type=text]').each(
            function () {
              jQuery(this).val('');
            }
          );

          idx = idx + 1;
          var oldId = '';
          var newId = '';
          var autoOption = false;
          line.find(':input').each(
            function () {
              if (this.id) {
                if (jQuery(this).hasClass('combobox')) {
                  oldId = this.id;
                }
                this.id = this.id.replace(/-new-id/, '-n' + idx);
                if (jQuery(this).hasClass('combobox')) {
                  newId = this.id;
                  autoOption = jQuery(this).autocomplete('option');
                }
              }
              this.name = this.name.replace(/\[NEW_ID\]/, '[' + (-1 * idx) + ']');
            }
          )
          line.removeClass('new').addClass('create-line');
          line.parent().append(newLine);
          core.trigger('attributes.modifiers.new', { element: newLine })
          if (autoOption) {
            var newInput = jQuery('#' + oldId).clone();
            newInput.attr('id', 'new_' + oldId)
            newInput.insertAfter('#' + oldId);
            jQuery('#' + oldId).remove();
            jQuery('#new_' + oldId).attr('id', oldId).autocomplete(autoOption);
            jQuery('#' + newId).autocomplete(autoOption);
          }
          line.parents('form').get(0).commonController.bindElements();
        }
      }
    );

    jQuery('.modifiers .popup').click(
      function (event) {
        event.stopPropagation();
      }
    );

    jQuery(document).click(
      function (e) {
        jQuery('.modifiers.open').each(
          function () {
            changeModifiers(jQuery(this))
          }
        );

        jQuery('.display-mode .value').each(
          function () {
            hideDisplayModeBlock(jQuery(this), e);
          }
        )
      }
    );

    jQuery('#save-mode').change(
      function () {
        if (jQuery(this).is(':checked')) {
          jQuery('form.attrs').addClass('view-changes');

        } else {
          jQuery('form.attrs').removeClass('view-changes');
        }
      }
    );

    jQuery('select,input,textarea').bind('change keyup focusin',
      function () {
        if ($(this).hasClass('display-mode-input')) {
          return;
        }

        var changed = this.initialValue != this.value;
        var el = jQuery(this);
        if (changed) {
          el.addClass('is-changed');
          el.parents('li.line.value').addClass('is-changed');
          el.parents('.attribute-name').addClass('is-changed');

        } else {
          el.removeClass('is-changed');
          el.parents('li.line.value').removeClass('is-changed');
          el.parents('.attribute-name').removeClass('is-changed');
        }
      }
    );

    jQuery('form.attrs').change(
      function () {
        if (jQuery(this).hasClass('changed') || jQuery(this).find('.line.is-changed').length) {
          jQuery('#save-mode').removeAttr('disabled');

        } else {
          jQuery('#save-mode').prop('disabled', 'disabled');
        }
      }
    );

    jQuery('.display-mode-link').click(
      function (e) {
        e.preventDefault();
        var $box = jQuery(this).closest('.display-mode');
        $box.toggleClass('expanded');
        $box.find('.value').toggle();
      }
    );

    jQuery('.display-mode-variant').click(
      function () {
        var $radioButton = jQuery(this).find('.table-value input');

        if ($radioButton.hasClass('clicked')) {
          $radioButton.removeClass('clicked');
          changeDisplayModeLink($(this));
        } else {
          $radioButton.trigger('click');
        }
      }
    );

    jQuery('.display-mode-variant input').click(
      function () {
        $(this).addClass('clicked');
      }
    );
  }
);

function changeDisplayModeLink($displayModeVariant) {
  var label = $displayModeVariant
    .find('label')
    .html();
  var $displayModeLink = $displayModeVariant
    .closest('.title')
    .find('.display-mode-link');

  $displayModeLink.html(label);
}

function hideDisplayModeBlock($displayModeBlock, e) {
  if (!$displayModeBlock.is(e.target)
    && $displayModeBlock.has(e.target).length === 0
    && !$displayModeBlock.siblings('.display-mode-link').is(e.target)
  ) {
    $displayModeBlock.hide();
    $displayModeBlock
      .closest('.display-mode')
      .removeClass('expanded');
  }
}

function changeModifiers(p) {
  var str = '';
  p.removeClass('open');
  p.find('input[type=text]').each(
    function () {
      if (jQuery(this).val()) {
        str = str + ' <span class="' + jQuery(this).data('type') + '-modifier">' + jQuery(this).val() + '</span>';
      }
    }
  );

  p.find('.default input[type=checkbox]:checked').each(
    function () {
      var def = jQuery(this);
      str = def.data('title') + (str ? ', ' : '') + str;
      p.parent().parent().parent().find('.modifiers').each(
        function () {
          var m = jQuery(this);
          m.find('.default input[type=checkbox]:checked').each(
            function () {
              if (jQuery(this).attr('name') != def.attr('name')) {
                jQuery(this).prop('checked', '');
                var text = m.find('span.text').html();
                text = text.replace(def.data('title') + ', ', '');
                text = text.replace(def.data('title'), '');
                m.find('span.text').html(text);
              }
            }
          );
        }
      );
    }
  );

  p.find('span.text').html(str);
  core.trigger('attributes.modifiers.change', { element: p })
}

function addAttribute(type, listId) {
  var box = jQuery('#list' + listId);

  idx = idx + 1;
  var line = jQuery('.create-tpl').clone(true);
  line.show()
    .removeClass('create-tpl')
    .addClass('create-line')
    .addClass('line')
    .find('.attribute-value, .display-mode').each(
      function () {
        if (!jQuery(this).hasClass('type-' + type.toLowerCase())) {
          jQuery(this).remove();
        }
      }
    );

  line.find(':input').each(
    function () {
      if (this.id) {
        this.id = this.id.replace(/-new-id/, '-n' + idx);
      }
      this.name = this.name.replace(/\[NEW_ID\]/, '[' + (-1 * idx) + ']');
      this.value = this.value.replace(/NEW_LIST_ID/, listId);
      this.value = this.value.replace(/NEW_TYPE/, type);
    }
  );

  box.append(line);
  line.parents('form').get(0).commonController.bindElements();
  box.parent().removeClass('empty');

  var form = box.parents('form').get(0);
  if (form) {
    form.commonController.bindElements();
  }
}
