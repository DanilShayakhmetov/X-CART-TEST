/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * @param element containing the navbar items
 * @constructor
 */
function TopMenuAutoHide(element) {
  this.$element = jQuery(element);
  this.element = this.$element.get(0);

  this.bindHandlers();

  this.init();
  this.updateMenu();
  setTimeout(_.bind(this.updateMenu, this), 200)
}

TopMenuAutoHide.prototype.element = null;
TopMenuAutoHide.prototype.$element = null;

var superBind = function (func, context) {
  return function () {
    _.partial(func, this).apply(context, arguments);
  }
};

TopMenuAutoHide.prototype.bindHandlers = function () {
  var handler = _.bind(this.updateMenu, this);

  jQuery(window).resize(handler);

  jQuery('.desktop-header')
    .bind('affixed-top.bs.affix', handler)
    .bind('affixed.bs.affix', handler);

  jQuery('.header_search-panel')
    .bind('show.bs.collapse', handler)
    .bind('hidden.bs.collapse', handler);

  jQuery('#logo img').bind('transitionend', handler);

  jQuery(this.$element).on('mouseenter', '.has-sub', superBind(this.checkSubMenuPosition, this));
};

TopMenuAutoHide.prototype.init = function () {
  var index = 0;
  this.$element.children(':not(.more)').each(function () {
    this.menuItemPosition = index++;
  })
};

TopMenuAutoHide.prototype.sortItems = function (items) {
  items.sort(function (a, b) {
    var ap = a.menuItemPosition;
    var bp = b.menuItemPosition;
    return ((ap < bp) ? -1 : ((ap > bp) ? 1 : 0));
  });

  return items;
};

/**
 * Recalculates more item position and content, hides extra elements, must be triggered on any navbar layout change
 */
TopMenuAutoHide.prototype.updateMenu = function () {
  var more = this.$element.find('.more');

  if (more.length) {
    more.find('ul:first>li').appendTo(this.$element);
    more.detach();
  } else {
    more = this.createMoreItem();
  }

  var menuItems = this.$element.children().detach().filter(function () {
    return jQuery(this).find('*').length;
  });

  var containerWidth = this.calculateNavbarWidth();
  this.$element.append(this.sortItems(menuItems));

  this.$element.children().removeClass('last');

  var extraPadding = this.getExtraPadding();

  while (
    this.$element.outerWidth() > containerWidth - extraPadding
    && this.$element.children(':not(.more)').length > 3
    ) {
    this.$element.children(':not(.more)').last().prependTo(more.find('ul:first'));
  }

  if (!more.find('ul:first > li').length) {
    more.remove();
  } else {
    more.appendTo(this.$element);

    if (this.$element.outerWidth() > containerWidth - extraPadding) {
      this.$element.children(':not(.more)').last().prependTo(more.find('ul:first'));
    }
  }

  this.$element.children().last().addClass('last');
  this.$element.closest('[data-desktop-navbar]').css('overflow', 'visible');
};

TopMenuAutoHide.prototype.createMoreItem = function () {
  return jQuery('<li class="leaf has-sub more"><span>' + core.t('More') + '</span><ul/><li>');
};

/**
 * @deprecated
 * @returns {number} parent container width
 */
TopMenuAutoHide.prototype.getContainerWidth = function () {
  return this.$element.parent().innerWidth();
};

/**
 * Calculates available navbar space via flex hacks. Only possible on empty navbar with flex-grow: 1.
 * @since 5.3.3.2
 * @returns {Number} width
 */
TopMenuAutoHide.prototype.calculateNavbarWidth = function () {
  var navbar = this.$element.closest('[data-desktop-navbar]');
  navbar.css('flex-basis', '1%');
  navbar.css('max-width', '100%');
  var width = navbar.innerWidth() - 1;
  navbar.css('flex-basis', 'auto');
  navbar.css('max-width', 'none');

  return width;
};

/**
 * @deprecated
 * @returns {number} width of the siblings
 */
TopMenuAutoHide.prototype.getSiblingsWidth = function () {
  var state = jQuery('.desktop-header').hasClass('affix-top') ? 'top' : 'affix';

  var width;
  if (state === 'top') {
    width = jQuery('.header-right-bar').outerWidth();

  } else if (state === 'affix') {
    var rightBarWidth = jQuery('.header-right-bar .header-right-bar').outerWidth();
    var searchWidth = 190; // FIXME: Hardcoded because search is initially hidden.
    var logo = $('.affix .company-logo');
    var logoWidth = logo.length ? logo.outerWidth() : 0;

    width = logoWidth + rightBarWidth + searchWidth;
  }

  return width;
};

/**
 * @returns {number} Get extra padding width
 */
TopMenuAutoHide.prototype.getExtraPadding = function() {
  var searchPanel = jQuery('.header-right-bar .header_search-panel').get(0);

  return searchPanel.getBoundingClientRect().width > 0
    ? searchPanel.getBoundingClientRect().width
    : 0
};

TopMenuAutoHide.prototype.checkSubMenuPosition = function (element, event) {
  var subMenu = jQuery('ul:first', element);
  if (event.type === 'mouseenter') {
    subMenu.removeClass('right');
    subMenu.toggleClass('right', subMenu.offset().left + subMenu.outerWidth() > window.innerWidth);
  }
};

core.autoload(TopMenuAutoHide, 'ul.top-main-menu');
