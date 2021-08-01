/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Left menu controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function LeftMenu() {
  var self = this;
  this.accordion = true;

  this.$menu = jQuery('#leftMenu');
  this.$body = jQuery('body');

  // this.headerHeight = jQuery('#header-wrapper').outerHeight();
  this.scrollTop = window.scrollY;

  jQuery(window)
    .scroll(_.partial(_.bind(this.recalculatePosition, this), undefined))
    .resize(_.partial(_.bind(this.recalculatePosition, this), undefined));

  this.$menu.on('affix-top.bs.affix', function () {
    jQuery(this).css('top', 0);
  });


  jQuery('.left-menu-ctrl').bind('click', _.bind(this.toggleMenu, this));

  jQuery('.menu .link', this.$menu).filter(function () {
    return jQuery(this).parent().nextAll('.box').length > 0;
  }).each(function () {
    var item = jQuery(this).closest('li');
    var box = jQuery(this).parent().nextAll('.box');
    var height = box.outerHeight();

    box.css('height', height);
    item.data('box-height', height);
    box.css('visibility', 'visible');
    box.css('position', 'relative');

    item.addClass(item.hasClass('pre-expanded') ? 'expanded' : 'collapsed').removeClass('pre-expanded');

    setTimeout(function () {
      box.css('transition', 'opacity .25s ease-in-out, height .25s ease-in-out');
    }, 300);

  }).bind('click', function (e) {
    if (self.$menu.hasClass('compressed')) {
      return true;
    }

    e.preventDefault();
    self.toggleItem(jQuery(this).closest('li'));
    return false;
  });

  jQuery('body').on('mouseenter', '#leftMenu.compressed > ul > li', function () {
    var box = jQuery('.box', this);
    if (box.length) {
      self.correctPosition(jQuery('.box', this));
    }
  });

  jQuery('body').on('click', '#leftMenu.compressed > ul > li:not(.empty) > .line > a', function (event) {
    event.preventDefault();
  });

  jQuery('body').on('click', '#leftMenu.compressed > ul > li:not(.empty) > .box li.title a.link', function (event) {
    event.preventDefault();
  });

  this.recalculatePosition();

  core.bind('recalculateLeftMenuPosition', function() {
    self.recalculatePosition()
  });

  if (this.$menu.hasClass('pre-compressed')) {
    this.$menu.removeClass('pre-compressed')
    this.$menu.addClass('compressed')
  }
}

LeftMenu.prototype.getWindowHeight = function () {
  return window.innerHeight;
};

LeftMenu.prototype.getScrollDelta = function () {
  var result = window.scrollY - this.scrollTop;
  this.scrollTop = window.scrollY;

  return result;
};

LeftMenu.prototype.getMenuHeight = function () {
  var result = 0;
  this.$menu.children().each(function () {
    result += jQuery(this).outerHeight();
  });

  result += parseInt(jQuery(this.$menu).css('padding-top'));

  return result;
};

LeftMenu.prototype.getHeaderSpace = function () {
  const headerSpace = jQuery('#header-wrapper').outerHeight() - window.scrollY;

  return headerSpace > 0 ? headerSpace : 0;
};

LeftMenu.prototype.getHeaderOffset = function () {
  return 0;
};

LeftMenu.prototype.recalculatePosition = function (heightDelta) {
  // don't do anything on overscroll
  if (document.body.scrollTop + window.innerHeight > document.body.scrollHeight) {
    return;
  }

  const sideBar = jQuery('#sidebar-first');

  const menuHeight = this.getMenuHeight() + (heightDelta | 0);
  const menuOffset = this.getHeaderOffset();
  const headerHeight = this.getHeaderSpace();
  const windowHeight = window.innerHeight;
  const scroll = window.scrollY;
  const isAffix = this.$menu.is('.affix');
  const topOffset = headerHeight + menuOffset;
  const minHeight = windowHeight - topOffset;

  let topPosition = menuOffset;
  let height = (menuHeight + topOffset) > windowHeight ? menuHeight : minHeight;

  if (isAffix) {
    topPosition = menuOffset - (scroll - menuOffset);

    if (height + topPosition < windowHeight) {
      topPosition = windowHeight - height;
    }
  }

  sideBar.css({ height: height });
  this.$menu.css({ 'min-height': minHeight, top: topPosition });

  if (this.$menu.is('.compressed')) {
    const visibleBox = jQuery('.box', this.$menu).filter(function () {
      return jQuery(this).css('visibility') === 'visible'
    });
    if (visibleBox.length) {
      this.correctPosition(visibleBox);
    }
  }

  if (heightDelta){
    this.recalculatePosition();
  }
};

LeftMenu.prototype.windowScroll = function () {

};

LeftMenu.prototype.windowResize = function () {

};

LeftMenu.prototype.toggleItem = function (element) {
  var delta = 0;

  if (element.hasClass('expanded')) {
    this.hideItem(element);
    delta -= element.data('box-height');
  } else {
    if (this.accordion) {
      var self = this;
      jQuery('.menu-item', this.$menu).each(function () {
        var item = jQuery(this);
        if (item.hasClass('expanded')) {
          delta -= item.data('box-height');
        }
        self.hideItem(item);
      });
    }
    this.showItem(element);
    delta += element.data('box-height')
  }

  this.recalculatePosition(delta);
};

LeftMenu.prototype.hideItem = function (element) {
  element.removeClass('expanded').addClass('collapsed');
  core.trigger('layout.sidebar.changeHeight');
};

LeftMenu.prototype.showItem = function (element) {
  element.addClass('expanded').removeClass('collapsed');
  core.trigger('layout.sidebar.changeHeight');
};

LeftMenu.prototype.toggleMenu = function () {
  if (this.$body.hasClass('left-menu-compressed')) {
    this.decompress();
  } else {
    this.compress();
  }

  return false;
};

LeftMenu.prototype.compress = function () {
  var box = jQuery('.menu div.box', this.$menu);
  box.hide();
  setTimeout(function () {
    box.show();
  }, 250);

  this.$menu.addClass('compressed');
  this.$body.addClass('left-menu-compressed');

  jQuery.cookie('XCAdminLeftMenuCompressed', 1);

  core.trigger('left-menu-compressed');
};

LeftMenu.prototype.decompress = function () {
  this.$menu.removeClass('compressed');
  this.$body.removeClass('left-menu-compressed');

  jQuery('.box', this.$menu).css('top', 0);

  jQuery.cookie('XCAdminLeftMenuCompressed', 0);
};

LeftMenu.prototype.correctPosition = function (box) {
  box.css({
    'top': 0,
    'bottom': 'auto'
  });

  var boxTop = box.offset().top;
  var boxBottom = boxTop + box.outerHeight();

  var viewportTop = window.scrollY;
  var viewportBottom = viewportTop + document.documentElement.offsetHeight;

  //calculate  modifier and move
  if (boxBottom > (viewportBottom - 10)) {
    box.css('top', viewportBottom - boxBottom - 20);

  } else if (boxTop < (viewportTop + 10)) {
    box.css('top', viewportTop - boxTop);
  }
};

core.autoload(LeftMenu);
