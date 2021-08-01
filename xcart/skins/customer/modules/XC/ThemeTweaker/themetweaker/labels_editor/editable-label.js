/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * editable language label controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var EditableLabel = Object.extend({

  constructor: function EditableLabel(element) {
    if (jQuery(element).closest('#themetweaker-panel').length > 0) {
      jQuery(element).removeClass('xlite-translation-label');
      return;
    }

    this.element = jQuery(element);
    this.element.attr('tabindex', -1);
    this.element.attr('role', 'button');

    this.template = _.template(EditableLabelTemplate);
    this.init();
    this.assignHandlers();
  },

  init: function () {
    this.popover = this.initializePopover();
    this.state = false;

    if (jQuery('[data-panel-switcher="data-panel-switcher"]').prop("checked")) {
      this.enable();
    }
  },

  disable: function () {
    EditableLabel.hidePopover(this.element);
    this.allowEvents();
    this.state = false;
    this.element.addClass('disabled');
  },

  enable: function () {
    this.suppressEvents();
    this.state = true;
    this.element.removeClass('disabled');
  },

  getPopoverOptions: function () {
    return {
      template: this.getPopoverTemplate(),
      content: _.partial(this.getPopoverContent, this),
      html: true,
      placement: 'auto left',
      title: this.element.data('xlite-label-name'),
      trigger: 'manual',
      container: 'body'
    }
  },

  getPopoverTemplate: function () {
    return '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-title"></div><div class="popover-content"></div></div>';
  },

  initializePopover: function () {
    this.element
      .popover(this.getPopoverOptions())
      .data('bs.popover')
      .tip()
      .addClass('xlite-translation-popover');

    return this.element.data('bs.popover').tip();
  },

  getPopoverContent: function (self) {
    return self.template({
      translation: _.escape(self.getTranslation(self.element)),
      substitutions: self.collectSubstitutions(),
    })
  },

  assignHandlers: function () {
    core.bind('editedLabel', _.bind(this.updateElement, this));
    core.bind('editable-label.disable', _.bind(this.disable, this));
    core.bind('editable-label.enable', _.bind(this.enable, this));

    this.element.on('click', _.bind(this.onElementClick, this));

    this.element.on('shown.bs.popover', _.bind(_.partial(this.onTogglePopover, true), this));
    this.element.on('hide.bs.popover', _.bind(_.partial(this.onTogglePopover, false), this));
  },

  onElementClick: function () {
    if (this.state) {
      this.element.popover('toggle');

      _.defer(_.bind(function () {
      }, this));
    }
  },

  suppressEvents: function () {
    var self = this;

    jQuery('button:not(.xlite-translation-button), a').filter(function () {
      return jQuery(this).closest('#themetweaker-panel').length == 0;
    }).css('pointer-events', 'none');

    this.element.css('pointer-events', 'auto');

    this.element.on('click', function (event) {
      if (self.state) {
        event.stopPropagation();
        event.preventDefault();
      }
    });
  },

  allowEvents: function () {
    jQuery('button:not(.xlite-translation-button), a').css('pointer-events', 'inherit');
  },

  onTogglePopover: function (state, event) {
    if (state) {
      if (event) {
        EditableLabel.closePopovers(event);
      }
      this.onShowPopover();
    } else {
      this.onHidePopover();
    }
  },

  onShowPopover: function () {
    var self = this;
    this.labelValue = this.getTranslation(this.element);
    this.element.addClass('edited');

    this.popover.find('[data-action=save]').on('click', _.bind(this.save, this));
    this.popover.find('[data-action=discard]').on('click', _.bind(this.discard, this));

    var val = this.popover.find('textarea.xlite-translation-wrapper').val();
    this.popover.find('textarea.xlite-translation-wrapper').focus().val('').val(val);

    this.popover.find('textarea.xlite-translation-wrapper').keydown(function (event) {
      if (event.keyCode === 27) {
        self.discard(event);
        return false;
      } else if ((event.metaKey || event.ctrlKey) && event.keyCode === 13) {
        self.save(event);
        return false;
      }

      return true;
    });
  },

  onHidePopover: function () {
    this.element.removeClass('edited');
  },

  save: function (event) {
    event.preventDefault();
    event.stopImmediatePropagation();

    var data = this.collectData();

    assignWaitOverlay(this.popover);

    core.post(
      URLHandler.buildURL({
        base: xliteConfig.admin_script,
        target: 'labels',
        action: 'edit'
      }),
      null,
      data
    )
      .done(_.bind(this.onSaveSuccess, this))
      .fail(_.bind(this.onSaveFailure, this));
  },

  onSaveSuccess: function () {
    unassignWaitOverlay(this.popover);
  },

  onSaveFailure: function () {
    unassignWaitOverlay(this.popover);
  },

  discard: function (event) {
    event.preventDefault();
    event.stopImmediatePropagation();

    EditableLabel.hidePopover(this.element);
  },

  getTranslation: function (element) {
    var copy = element.clone();

    jQuery('.xlite-translation-var', copy).replaceWith(function () {
      var key = jQuery(this).data('xlite-var-key');
      return "{{" + key + "}}";
    });

    return copy.html();
  },

  collectData: function () {
    var wrapper = this.popover.find('.xlite-translation-wrapper');

    var label = {};
    label[this.element.data('xlite-label-code')] = _.unescape(this.getInputValue());

    return {
      label_name: this.element.data('xlite-label-name'),
      code: this.element.data('xlite-label-code'),
      label: label,
      substitutions: this.collectSubstitutions(),
    };
  },

  collectSubstitutions: function () {
    if ('undefined' === typeof this.substitutions) {
      this.substitutions = this.element.find('.xlite-translation-var').toArray().reduce(
        function (acc, item) {
          var elem = jQuery(item);
          acc[elem.data('xlite-var-key')] = elem.text();
          return acc;
        }, {}
      );
    }

    return this.substitutions;
  },

  distributeSubstitutions: function (element) {
    var self = this;

    element.find('.xlite-translation-var').each(function () {
      var key = jQuery(this).data('xlite-var-key');
      var value = self.collectSubstitutions()[key];

      if (value && key) {
        jQuery(this).html(value);
      }
    });

    return element;
  },

  getInputValue: function () {
    return this.popover.find('.xlite-translation-wrapper').val();
  },

  updateElement: function (event, data) {
    if (data.name === this.element.data('xlite-label-name')) {
      var updated = this.distributeSubstitutions(jQuery(data.translation));
      this.element.html(updated.html());
      this.init();

      EditableLabel.hidePopover(this.element);
      core.trigger('editable-label.enable');
    }
  }
});

EditableLabel.hidePopover = function (element) {
  (($(element).popover('hide').data('bs.popover') || {}).inState || {}).click = false;
};

EditableLabel.closePopovers = function (e) {
  $('.xlite-translation-label.edited').each(function () {
    //the 'is' for buttons that trigger popups
    //the 'has' for icons within a button that triggers a popup
    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
      EditableLabel.hidePopover(this);
    }
  })
};