/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * asdas
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ProgressLoadable(base, jobId)
{
  var args = Array.prototype.slice.call(arguments, 0);

  this.controller = null;
  this.jobId = jobId;
  this.callSupermethod('constructor', args);
}

extend(ProgressLoadable, ALoadable);

// Shade widget
ProgressLoadable.prototype.shadeWidget = true;

// Widget target
ProgressLoadable.prototype.widgetTarget = 'jobs';

// Widget class name
ProgressLoadable.prototype.widgetClass = 'XLite\\View\\Job\\Progress';

ProgressLoadable.prototype.postprocess = function(isSuccess, initial)
{
  ProgressLoadable.superclass.postprocess.apply(this, arguments);

  if (isSuccess && !initial) {
    var jobId = this.base.data('job-id');
    this.controller = new ProgressController(jobId, this.base, false, false);
    core.trigger('jobs.add_job', jobId)
  }
};

// Load after page load
core.autoload(ProgressLoadable);
