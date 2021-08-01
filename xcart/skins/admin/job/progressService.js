/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var ProgressService = Object.extend({
  runner:       null,
  serviceName:  'cumulative_progress',

  constructor: function ProgressService() {
    this.ids = [];
    core.bind('backendRunner.success', _.bind(this.processResult, this));
    core.bind('jobs.add_job', _.bind(this.addJob, this));
    core.bind('jobs.remove_job', _.bind(this.removeJob, this));

    this.startCumulativeRunner();
  },

  startCumulativeRunner: function() {
    if (this.runner) {
      this.runner.start();

      return;
    }

    var self = this;

    var endpoint = this.generateEndpoint();

    core.bind('backendRunner.initialized', function(event, data) {
      if (data.serviceName === self.serviceName) {
        self.runner = data;
        self.runner.start();
      }
    });

    core.trigger('backendRunner.run', {
      endpoint:     endpoint,
      serviceName:  self.serviceName,
      intervalRate: 1500,
    });
  },

  stop: function() {
    if (this.runner) {
      this.runner.stop();
    }
  },

  addJob: function(event, jobId) {
    if (!this.ids || !this.ids.length) {
      this.startCumulativeRunner();
    }
    this.ids.push(jobId);
    this.runner.setEndpoint(this.generateEndpoint());
  },

  removeJob: function(event, jobId) {
    this.ids = _.without(this.ids, jobId);

    this.runner.setEndpoint(this.generateEndpoint());
    if (!this.ids || !this.ids.length) {
      this.stop();
    }
  },

  generateEndpoint: function () {
    var data = {
      target: 'jobs',
      action: 'get_progress_multiple'
    };

    if (this.ids) {
      data['ids'] = this.ids;
    }

    return URLHandler.buildURL(data);
  },

  processResult: function(event, data) {
    if (data.runner && data.runner.serviceName !== this.serviceName) {
      return;
    }

    var payload = data.data;

    if (payload) {
      _.each(payload, _.bind(this.processSingleResult, this));
    }
  },

  processSingleResult: function(jobData, jobId) {
    core.trigger('jobs.progress_received', {
      id:   jobId,
      data: jobData
    });
  },

});

core.bind('bouncingRunner.ready', function() {
  window.progressService = new ProgressService();
});
