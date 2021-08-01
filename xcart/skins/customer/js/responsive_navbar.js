/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Sticky footer
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function() {

  function loadWidget(widget, selector, dfd) {
    core.get({
      target: "main",
      widget: widget
    }).done(function(data) {
      if ($(selector).children().length > 0) {
        dfd.resolve();
        return;
      }

      var widget = $(data);

      var uuid = _.uniqueId();

      core.bind(['resources.ready', 'resources.empty'], _.bind(
        function(event, args){
          if (args.uuid === uuid) {
            var newContent = widget.find(selector);
            $(selector).replaceWith(newContent);

            dfd.resolve();
          }
        },
        this)
      );

      core.parsePreloadedLabels(widget, uuid);
      core.parseResources(widget, uuid);
    });
  }

  function loadDesktopNavbar() {
    var dfd = new jQuery.Deferred();
    var responsiveClass = getResponsiveClass(document.body);

    if (responsiveClass !== 'mobile' || !responsiveClass) {
      dfd.resolve();
    } else {
      loadWidget("XLite\\View\\Layout\\Customer\\DesktopNavbar", "[data-desktop-navbar]", dfd);
    }

    return dfd;
  }

  function loadMobileNavbar() {
    var dfd = new jQuery.Deferred();
    var responsiveClass = getResponsiveClass(document.body);

    if (responsiveClass !== 'desktop' || !responsiveClass) {
      dfd.resolve();
    } else {
      loadWidget("XLite\\View\\Slidebar", "[data-mobile-navbar]", dfd);
    }

    return dfd;
  }

  function getResponsiveClass(element) {
    var matches = element.className.match(/responsive-(\w+)/); //get a match to match the pattern some-class-somenumber and extract that classname

    if (matches) {
      return matches[1];
    }

    return null;
  }

  function loadNavbars() {
    loadDesktopNavbar().then(function() {
      core.trigger('navbar.desktop.loaded');
    });

    loadMobileNavbar().then(function() {
      core.trigger('navbar.mobile.loaded');
    });
  }

  loadNavbars();
})();