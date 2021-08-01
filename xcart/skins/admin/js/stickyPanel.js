/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky panel controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StickyPanel(base)
{
  base = jQuery(base);
  if (0 < base.length && base.hasClass('sticky-panel') && !base.get(0).controller) {
    base.get(0).controller = this;
    this.base = base;

    this.process();
    if (!this.isFormDoNotChangeActivation()) {
      this.unmarkAsChanged();
      this.unmarkMoreActionAsEnabled();
    }
  }
}

extend(StickyPanel, Base);

// Autoloader
StickyPanel.autoload = function()
{
  jQuery('.sticky-panel')
    .not('.another-sticky')
    .not('.model-list')
    .each(
      function() {
        new StickyPanel(this);
      }
    );
};

// Default options
StickyPanel.prototype.defaultOptions = {
  bottomPadding:       0,
  parentContainerLock: true
};

// Panel
StickyPanel.prototype.panel = null;

// Timer resource
StickyPanel.prototype.timer = null;

// Current document
StickyPanel.prototype.doc = null;

StickyPanel.prototype.moreActionAsEnabled = false;

// Process widget (initial catch widget)
StickyPanel.prototype.process = function()
{
  // Initialization
  this.panel = this.base.find('.box').eq(0);

  this.base.height(this.panel.outerHeight());

  if (!this.isModal()) {
    this.reposition();

    core.bind('stickyPanelReposition', _.bind(this.reposition, this));
  }

  // Form change activation behavior
  if (this.isFormChangeActivation()) {
    var form = this.base.parents('form').eq(0);
    form.bind(
      'state-changed',
      _.bind(this.markAsChanged, this)
    );
    form.bind(
      'state-initial',
      _.bind(this.unmarkAsChanged, this)
    );
    form.bind(
      'more-action-enabled',
      _.bind(this.markMoreActionAsEnabled, this)
    );
    form.bind(
      'more-action-initial',
      _.bind(this.unmarkMoreActionAsEnabled, this)
    );
    form.bind(
      'submit',
      _.bind(this.blockSubmitButton, this)
    )
  }

  this.fixMoreActionButtons();
};

StickyPanel.prototype.reposition = function()
{
};

// Check - sticky panel in dialog widget or not
StickyPanel.prototype.isModal = function()
{
  return this.base.parents('.ui-dialog').length > 0
    || this.base.parents('.ajax-container-loadable').length > 0;
};


// Get options
StickyPanel.prototype.getOptions = function()
{
  var options = this.base.data('options') || {};

  jQuery.each(
    this.defaultOptions,
    function (key, value) {
      if ('undefined' == typeof(options[key])) {
        options[key] = value;
      }
    }
  );

  return options;
};

// Check - form change activation behavior
StickyPanel.prototype.isFormChangeActivation = function()
{
  return this.base.hasClass('form-change-activation');
};

// Check - form present but do not change activation
StickyPanel.prototype.isFormDoNotChangeActivation = function()
{
  return this.base.hasClass('form-do-not-change-activation');
};

// Mark as changed
StickyPanel.prototype.markAsChanged = function(event, data)
{
  this.triggerVent('markAsChanged', { 'widget': this });
  this.getSubmitButton().removeClass('blocked');

  if (!this.isFormDoNotChangeActivation()) {
    this.getFormChangedButtons().each(
      _.bind(
        function (index, button) {
          this.enableButton(button);
        },
        this
      )
    );
  }

  this.getFormChangedLinks().removeClass('disabled');
};

// Unmark as changed
StickyPanel.prototype.unmarkAsChanged = function()
{
  this.triggerVent('unmarkAsChanged', { 'widget': this });

  this.getFormChangedButtons().each(
    _.bind(
      function(index, button) {
        this.disableButton(button);
      },
      this
    )
  );

  this.getFormChangedLinks().addClass('disabled');
};

// Mark as changed
StickyPanel.prototype.markMoreActionAsEnabled = function()
{
  if (!this.moreActionAsEnabled) {
    this.getMoreActionButtons().each(
      _.bind(
        function(index, button) {
          this.enableButton(button);
        },
        this
      )
    );

    this.fixMoreActionButtons();

    this.moreActionAsEnabled = true;
  }
};

// Unmark as changed
StickyPanel.prototype.unmarkMoreActionAsEnabled = function()
{
  if (this.moreActionAsEnabled) {
    this.getMoreActionButtons().each(
      _.bind(
        function(index, button) {
          if (!jQuery(button).hasClass('always-enabled')) {
            this.disableButton(button);
          }
        },
        this
      )
    );

    this.fixMoreActionButtons();

    this.moreActionAsEnabled = false;
  }
};

// Enable button
StickyPanel.prototype.enableButton = function(button)
{
  var state = {
    'state':   true,
    'inverse': false,
    'button':  button,
    'widget':  this
  };

  this.triggerVent('check.button.enable', state);

  if (state.state) {
    if (button.enable) {
      button.enable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).removeClass('hidden');
    }

  } else if (state.inverse) {
    if (button.disable) {
      button.disable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).addClass('hidden');
    }
  }
};

// Disable button
StickyPanel.prototype.disableButton = function(button)
{
  var state = { 'state': true, 'inverse': false, 'button': button, 'widget': this };

  this.triggerVent('check.button.disable', state);

  if (state.state) {
    if (button.disable) {
      button.disable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).addClass('hidden');
    }

  } else if (state.inverse) {
    if (button.enable) {
      button.enable();
    }
    if (jQuery(button).is('.hide-on-disable')) {
      jQuery(button).removeClass('hidden');
    }
  }
};

// Get a form button, which should change as the state of the form
StickyPanel.prototype.getFormChangedButtons = function()
{
  var buttons = this.base.find('button, div.divider');

  // If there is any element inside the dropdown menu with the "always-enabled" state
  // then we do not disable the toggle list action button
  return (this.base.find('.dropdown-menu .always-enabled').length > 0)
    ? buttons.not('.always-enabled, .toggle-list-action, .more-action, .more-actions, .blocked')
    : buttons.not('.always-enabled, .more-action, .more-actions, .blocked');
};

StickyPanel.prototype.getMoreActionButtons = function()
{
  return this.base.find('.more-action, .more-actions');
};

StickyPanel.prototype.getSubmitButton = function()
{
  return this.base.find('button[type=submit]');
};

StickyPanel.prototype.blockSubmitButton = function ()
{
  this.getSubmitButton().each(
    _.bind(
      function(index, button) {
        this.disableButton(button);
      },
      this
    )
  );
  this.getSubmitButton().addClass('blocked');
};

StickyPanel.prototype.fixMoreActionButtons = function()
{
  this.getMoreActionButtons().removeClass('first-visible').removeClass('last-visible')
    .filter(':visible')
    .first().addClass('first-visible')
    .end()
    .last().addClass('last-visible');

  this.base.find('.additional-buttons').parent()
    .toggleClass('additional-hidden', !this.getMoreActionButtons().filter(':visible').length);
};

// Get a form links, which should change as the state of the form
StickyPanel.prototype.getFormChangedLinks = function()
{
  return this.base.find('.cancel');
};

// Get event namespace (prefix)
StickyPanel.prototype.getEventNamespace = function()
{
  return 'stickypanel';
};

// Autoload
core.microhandlers.add(
  'sticky-panel',
  '.sticky-panel',
  function () {
    core.autoload(StickyPanel);
  }
);
