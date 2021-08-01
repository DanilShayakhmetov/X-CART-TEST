/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function slidebar() {
  var self = this;
  jQuery(function () {
    core.trigger('mm-menu.before_create', jQuery('#slidebar'));
    jQuery('#slidebar').mmenu(self.options, self.configuration);

    var api = jQuery('#slidebar').data('mmenu');

    if (!_.isUndefined(api)) {
      core.trigger('mm-menu.created', api);
      api.bind('closed', function () {
        api.closeAllPanels();
      });
    }

    jQuery('.dropdown-menu#search_box').parent().on('shown.bs.dropdown', function () {
      jQuery('#header').addClass('hidden');
    });

    jQuery('.dropdown-menu#search_box').parent().on('hidden.bs.dropdown', function () {
      jQuery('#header').removeClass('hidden');
    });
  });
}

slidebar.prototype.options = {
  extensions: ['pagedim-black'],
  navbar: {
    title: core.t('Menu')
  },
  offCanvas: {
    pageSelector: "#page-wrapper"
  }
};

slidebar.prototype.configuration = {
  panelNodetype: "ul, ol"
};

core.bind('navbar.mobile.loaded', function () {
  core.autoload(slidebar);
});
