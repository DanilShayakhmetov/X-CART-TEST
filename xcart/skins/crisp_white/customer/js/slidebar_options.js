/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

slidebar.prototype.options = _.extend(slidebar.prototype.options, {
  offCanvas: _.extend(slidebar.prototype.options.offCanvas, {
    position: "right",
    zposition: "front"
  }),
  navbar: _.extend(slidebar.prototype.options.navbar, {
    add: true,
    title: '',
  }),
  navbars: [
    {
      position: "top",
      content: getNavbarItems()
    },
    {
      position: "top",
      content: ["title"]
    }
  ]
});

core.bind('mm-menu.before_create', function(event, element) {
  if (element.find('#settings-panel ul').length) {
    element.find('#settings-panel ul').addClass('Inset');
  }
});

core.bind('mm-menu.created', function(event, api){
  api.bind('openPanel', function ($panel) {
    if ($panel.is('.mm-panel:first')) {
      $panel.parent('#slidebar').addClass('first-opened');
    } else {
      $panel.parent('#slidebar').removeClass('first-opened');
    }

    const navbarAccountIcon = jQuery('.navbar-account span');
    if ($panel.attr('id') == 'account-navbar-panel') {
      navbarAccountIcon.addClass('active');
    } else {
      navbarAccountIcon.removeClass('active');
    }

    const navbarSettingsLabel = jQuery('.navbar-settings span');
    if ($panel.attr('id') == 'settings-navbar-panel') {
      navbarSettingsLabel.addClass('active');
    } else {
      navbarSettingsLabel.removeClass('active');
    }

    if ($panel.find('.mm-title').length && $panel.find('.mm-title').html()) {
      jQuery('.mm-navbar-top-2').show();
      $panel.css("padding-top", "90px");
    } else {
      jQuery('.mm-navbar-top-2').hide();
    }
  });

  api.bind('open', function () {
    jQuery('#slidebar').addClass('first-opened');
  });

  jQuery('#slidebar button.popup-button').on('popup.open', function () {
    var mmenu = jQuery('#slidebar').data('mmenu');

    if (mmenu) {
      mmenu.close();
    }
  });

  slidebarItemsReposition();
  removeAccountFromMainMenu();
  addCompareIndicator();
});

function getNavbarItems() {
  const navbarItems = [
    "prev",
    "<a class='navbar-account' href='#account-navbar-panel'><span class='icon-account'></span></a>"
  ];

  var showSettingsNavbar = false;
  var settingsNavbarLabel = "";

  const currency = jQuery('.currency-indicator');
  if (currency.length) {
    showSettingsNavbar = true;
    settingsNavbarLabel += '<span>' + currency.html() + '</span>';
  }

  const language = jQuery('.language-indicator');
  if (language.length) {
    showSettingsNavbar = true;
    settingsNavbarLabel += '<span>' + language.html() + '</span>';
  }

  if (showSettingsNavbar) {
    navbarItems.push("<a class='navbar-settings' href='#settings-navbar-panel'>" + settingsNavbarLabel + "</a>");
  }

  return navbarItems;
}

function slidebarItemsReposition() {
  const menuCategory = jQuery('#slidebar .mm-listview .slidebar-categories');
  const menuHome = jQuery('#slidebar .mm-listview .first');
  menuHome.insertBefore(menuCategory);
}

function removeAccountFromMainMenu() {
  const accountAnchor = jQuery('#slidebar .mm-listview .leaf.has-sub a[href="cart.php?target=order_list"]');
  accountAnchor.closest("li").remove();
}

function addCompareIndicator() {
  const compareIndicator = jQuery('#slidebar #account-navbar-panel .compare-indicator');
  if (compareIndicator.hasClass('recently-updated')) {
    const iconAccount =jQuery('#slidebar .icon-account');
    iconAccount.addClass('recently-updated-icon');
  }
}