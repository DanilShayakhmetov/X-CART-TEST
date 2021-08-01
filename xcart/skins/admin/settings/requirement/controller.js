/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Widget
 */
function RequirementView(base)
{
  this.callSupermethod('constructor', arguments);
  this.widgetParams = core.getCommentedData(base, 'reloadParams');
}

extend(RequirementView, ALoadable);

RequirementView.batchSize = 1;

RequirementView.autoload = function() {
  jQuery('.critical-requirements-table, .non-critical-requirements-table').each(function(i, table) {
    const lines = jQuery(table).find('.requirement-line');
    let promise = $.when();
    let batch = [];

    lines.each(function(index, elem) {
      const view = new RequirementView(elem);
      view.shade();

      batch.push(() => view.load());

      if (batch.length === RequirementView.batchSize || index === lines.length - 1) {
        const currentBatch = batch;
        promise = promise.then(() => Promise.all(currentBatch.map(task => task())));
        batch = [];
      }
    });
  });
};

// No shade widget
RequirementView.prototype.shadeWidget = true;

RequirementView.prototype.base = '.requirement-line';
// Widget target
RequirementView.prototype.widgetTarget = 'cache_management';

// Extract widget content
RequirementView.prototype.extractContent = function(box)
{
  box = jQuery(this.containerRequestPattern, box);

  if (box.children().eq(0).hasClass('block')) {
    box = jQuery('.block:first .real-content', box.eq(0));
  }

  return box;
};
// Widget class name
RequirementView.prototype.widgetClass = '\\XLite\\View\\Requirement';

RequirementView.prototype.getLoaderOptions = function()
{
  var list = ALoadable.prototype.getLoaderOptions.apply(this, arguments);
  list.timeout = 120 * 1000;

  return list;
};

core.autoload(RequirementView);
