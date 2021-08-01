/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Template selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var TemplatesSelector = Object.extend({
  constructor: function TemplatesSelector (base) {
    this.base = $(base);
    this.base.commonController = this;

    this.selector = jQuery('.hidden-field select', this.base);
    this.form = this.base.closest('form');

    $('.template', this.base).on('click', this.onTemplateClick.bind(this));
    $('.template.marked', this.base).addClass('active');

    this.form.on('submit', this.onSubmit.bind(this));
  },

  onTemplateClick: function (event) {
    $('.template', this.base).removeClass('checked');

    var templateId = $(event.currentTarget).addClass('checked').data('template-id');
    this.selectTemplate(templateId);

    var settingsWidget = $('.layout-settings.settings');

    if (this.selector.parents('form').get(0).isChanged()) {
      window.assignShadeOverlay(settingsWidget);
    } else {
      window.unassignShadeOverlay(settingsWidget);
    }
  },

  onSubmit: function (event) {
    var isRedeployRequired = typeof $('.template.checked', this.base).data('is-redeploy-required') !== 'undefined';
    var confirmMsg = core.t('To make your changes visible in the customer area, cache rebuild is required. It will take several seconds. You don’t need to close the storefront, the operation is executed in the background.');
    if (isRedeployRequired && !confirm(confirmMsg)
    ) {
      event.stopPropagation();
      event.preventDefault();

      return false;
    }
  },

  selectTemplate: function (template) {
    this.selector.val(template);
    this.selector.trigger('change');
  }
})

core.autoload(TemplatesSelector, '.change-template .templates');
