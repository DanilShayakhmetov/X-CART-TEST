/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ListContainer(element) {
  this.base = $(element);
  this.instances = [];
  this.weightStep = 10;

    var self = this;
    var groups = $(this.base).children('.list-items-group');

    var savedState = sessionStorage.getItem('layout-editor-mode');

  if (savedState === null) {
    savedState = true;
  } else {
    savedState = (savedState === "true");
  }

  this.base.toggleClass('disabled', !savedState);

  groups.each(function () {
    var groupName = $(self.base).data('group') || '';
    jQuery(this).data('group', groupName);
    var instance = Sortable.create(this, {
      animation: 150,
      group: 'common',
      disabled: !savedState,
      filter: ".list-item-actions, .list-item-action",
      onStart: _.bind(self.onStart, self),
      onEnd: _.bind(self.onEnd, self),
      onAdd: _.bind(self.onAdd, self),
      onUpdate: _.bind(self.onUpdate, self),
      onRemove: _.bind(self.onRemove, self),
      onFilter: _.bind(self.onFilter, self),
      onMove: _.bind(self.onMove, self),
      forceFallback: true,
      scroll: document.querySelector('#main'), // or HTMLElement
      scrollSensitivity: 200, // px, how near the mouse must be to an edge to start scrolling.
      scrollSpeed: 25, // px
    });

    self.instances.push(instance);
  });
}

ListContainer.prototype.move = function (item) {
  var from = item.closest('.list-items-group');
  var center = $('.list-items-group[data-list="center"]');

  if (center.length > 0) {
    center.append(item);
    var event = new Event('add');
    event.from = from;
    event.to = center;
    event.item = item;
    this.onAdd(event);
  } else {
    console.error('Can\'t move item to center group, because it doesn\'t exist');
  }
};

ListContainer.prototype.toggle = function (state) {
  this.instances.forEach(function (item) {
    item.option('disabled', !state);
  });

  this.base.toggleClass('disabled', !state);
};

ListContainer.prototype.onStart = function (event) {
  core.trigger('layout.dragStart');
  $('.list-item').addClass('list-item__not-hoverable');
  $('.list-items-group').addClass('list-items-group__on-move');
};


ListContainer.prototype.onEnd = function (event) {
  $('.list-item').removeClass('list-item__not-hoverable');
  $('.list-items-group').removeClass('list-items-group__on-move');
  core.trigger('layout.dragStop');
};


ListContainer.prototype.onAdd = function (event) {
  var oldViewlist = $(event.from).data('list');
  var newViewlist = $(event.to).data('list');
  var displayGroup = $(event.to).data('group');
  var itemId = $(event.item).data('id');
  var newPosition = this.calculateWeight(event.item);
  var oldDisplayGroup = jQuery(event.item).data('display');

  if (!_.isEmpty(oldDisplayGroup) && displayGroup !== oldDisplayGroup) {
    jQuery(event.item).data('display', displayGroup);

    core.trigger(
      'layout.block.reload',
      {
        id: itemId,
        displayGroup: displayGroup,
      }
    );
  }

  core.trigger(
    'layout.moved',
    {
      id: itemId,
      from: oldViewlist,
      list: newViewlist,
      position: newPosition
    }
  );
};


ListContainer.prototype.onUpdate = function (event) {
  var list = $(event.item).data('list') || $(event.to).data('list');
  var itemId = $(event.item).data('id');
  var newPosition = this.calculateWeight(event.item);
  core.trigger(
    'layout.rearranged',
    {
      id: itemId,
      list: list,
      was: event.oldIndex,
      position: newPosition
    }
  );
};

ListContainer.prototype.calculateWeight = function (element) {
  var prev = parseInt($(element).prev().data('weight')) || 0;
  var next = parseInt($(element).next().data('weight')) || 0;
  var weight;

  if (next) {
    weight = Math.ceil((next + prev) / 2);
  } else {
    weight = prev + this.weightStep
  }

  $(element).data('weight', weight);

  this.recalculateWeightsRecursive(element);

  return weight;
};

ListContainer.prototype.recalculateWeightsRecursive = function (element) {
  const weight = $(element).data('weight');
  const $next = $(element).next();

  if ($next.length && weight >= $next.data('weight')) {
    const newPosition = weight + this.weightStep;

    $next.data('weight', newPosition);

    core.trigger(
      'layout.rearranged',
      {
        id: $next.data('id'),
        list: $next.data('list'),
        position: newPosition
      }
    );

    this.recalculateWeightsRecursive($next);
  }
};

ListContainer.prototype.onRemove = function (event) {

};


ListContainer.prototype.onFilter = function (event) {

};

ListContainer.prototype.onMove = function (event) {

};

core.autoload(ListContainer, '.list-container');
