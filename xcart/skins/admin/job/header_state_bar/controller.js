/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var HeaderJobsStateController = Object.extend({
  reportReaderRunner: null,

  constructor: function HeaderJobsStateController(base) {
    this.base = base;

    this.assignHandlers();

    this.requestForJobs();
  },

  assignHandlers: function() {
    core.bind('jobs.trackProgress', _.bind(this.startJobProgress, this));
    core.bind('progress.stop', _.bind(this.finishJobProgressTracking, this));
    core.bind('jobs.received', _.bind(this.processJobs, this));
  },

  requestForJobs: function() {
    var endpoint = URLHandler.buildURL({
      target: 'jobs',
      action: 'getJobs'
    });

    core.get(
        endpoint,
        function(xhr, status, data) {
          if (xhr.readyState != 4 || xhr.status != 200) {
            console.log('Event task touch procedure internal error');

          } else {
            core.trigger('jobs.received', jQuery.parseJSON(data));
          }
        },
        {},
        {}
    );
  },

  processJobs: function(event, data) {
    _.each(data.jobs, function(jobId) {
      core.trigger('jobs.trackProgress', {
        jobId: jobId
      })
    });
  },

  startJobProgress: function (event, data) {
    if (jQuery('.job-progress.id-' + data.jobId).length) {
      return;
    }

    this.addJobProgress(data.jobId);
  },

  addJobProgress: function (jobId) {
    var progressBlock = jQuery('<div></div>');
    jQuery(this.base).prepend(progressBlock);
    var loadable = new ProgressLoadable(progressBlock);

    loadable.load({
      jobId: jobId
    });
  },

  finishJobProgressTracking: function (event, data) {
    var jobId = data.jobId;

    var jobBlock = jQuery('.job-progress.id-' + jobId);
    jobBlock.fadeOut(500, function() {
      jobBlock.remove();
    });
  },

});

jQuery().ready(
    function() {
      var base = jQuery('.jobs-tray-wrapper');

      if (base) {
        window.headerTrayJobsController = new HeaderJobsStateController(base);
      }
    }
);
