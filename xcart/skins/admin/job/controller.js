/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var ProgressController = Object.extend({
  jobId:                null,
  reportReaderRunner:   null,
  jobsJsRunner:         null,
  startTime:            null,
  lastProgressData: {
    lastReceived:   Date.now(),
    progress:       0
  },

  constructor: function ProgressController(jobId, base, shouldStartConsumerRunner, shouldStartProgressRunner) {
    this.jobId = jobId;
    this.base = base || jQuery('.job-progress.id-' + this.jobId);

    this.assignCancelButton();

    core.bind('backendRunner.success', _.bind(this.processResult, this));

    if (shouldStartConsumerRunner === true || _.isUndefined(shouldStartConsumerRunner)) {
      this.startConsumerRunner();
    }

    if (shouldStartProgressRunner === true || _.isUndefined(shouldStartProgressRunner)) {
      this.startProgressRunner();
    } else {
      core.bind('jobs.progress_received', _.bind(this.processProgressResult, this));
    }
  },

  assignCancelButton: function() {
    var cancelButton = jQuery('.cancel', this.base);

    cancelButton.click(_.bind(this.cancel, this));
  },

  startConsumerRunner: function() {
    var self = this;
    var uid = this.jobId;

    core.bind('backendRunner.initialized', function(event, data) {
      if (data.serviceName === 'jobs_js_runner_' + uid) {
        self.jobsJsRunner = data;
        self.jobsJsRunner.start();
        self.startTime = Date.now();
      }
    });

    core.trigger('backendRunner.run', {
      endpoint:     URLHandler.buildURL({ target: 'jobs', action: 'run' }),
      serviceName:  'jobs_js_runner_' + uid,
    });
  },

  startProgressRunner: function () {
    var self = this;
    var uid = this.jobId;

    core.bind('backendRunner.initialized', function(event, data) {
      if (data.serviceName === 'report_reader_' + uid) {
        self.reportReaderRunner = data;
        self.reportReaderRunner.start();
      }
    });

    core.trigger('backendRunner.run', {
      endpoint:     URLHandler.buildURL({ target: 'jobs', action: 'get_progress', id: this.jobId}),
      serviceName:  'report_reader_' + uid,
      intervalRate: 750
    });
  },

  processResult: function(event, data) {
    if (data.runner && data.runner.serviceName !== 'report_reader_' + this.jobId) {
      return;
    }

    var payload = data.data;

    if (payload && 'undefined' !== typeof(payload.progress)) {
      this.processProgressResult(payload);
    }
  },

  processProgressResult: function(event, data) {
    if (data.id !== this.jobId) {
      return;
    }

    var payload = data.data;

    this.updateProgress(payload.progress);
    var estimatedTime = this.getEstimated(payload.progress);
    this.updateEstimation((this.lastProgressData.lastEstimation + estimatedTime) / 2);

    this.lastProgressData = {
      lastReceived:   Date.now(),
      progress:       payload.progress,
      lastEstimation: estimatedTime
    };

    if (intval(payload.progress) > 0 || payload.isStarted === true) {
      this.markAsStarted();
    }

    if (intval(payload.progress) >= 100 || payload.isFinished === true) {
      this.updateProgress(100);
      this.stop();
    }
  },

  getEstimated: function(progress) {
    var elapsed = (Date.now() - this.lastProgressData.lastReceived) / 1000;
    var timePerPercent = elapsed / (progress - this.lastProgressData.progress);

    return (100 - progress) * timePerPercent;
  },

  updateEstimation: function(estimated) {
    var estimationBlock = jQuery('.estimated-time', this.base);
    estimationBlock.html('About ' + intval(estimated) + ' second(s) remaining');
  },

  updateProgress: function(progress) {
    var progressBar = jQuery('.progress-bar', this.base);
    progressBar.css('width', progress + '%');
  },

  markAsStarted: function() {
    var progressBar = jQuery('.progress-bar', this.base);
    progressBar.addClass('no-transition');
    progressBar.removeClass('waiting');
    _.delay(function () {
      progressBar.removeClass('no-transition');
    }, 350);
  },

  cancel: function () {
    var endpoint = URLHandler.buildURL({
      'target':   'jobs',
      'action':   'cancel',
      'id':       this.jobId
    });

    var self = this;
    core.get(
        endpoint,
        function(xhr, status, data) {
          if (xhr.readyState != 4 || xhr.status != 200) {
            console.log('Event task touch procedure internal error');

          } else {
            self.stop();

            var estimationBlock = jQuery('.estimated-time', this.base);
            estimationBlock.html('Job has been cancelled');
            core.trigger('progress.cancelled');
          }
        },
        {},
        {timeout: 5000}
    );
  },

  stop: function () {
    core.trigger('progress.stop', {
      jobId: this.jobId
    });

    core.trigger('jobs.remove_job', this.jobId);

    if (this.reportReaderRunner) {
      this.reportReaderRunner.stop();
    }

    if (this.jobsJsRunner) {
      this.jobsJsRunner.stop();
    }
  }

});

jQuery().ready(
    function() {
      var jobId = jQuery('.job-progress.self-initiated').data('job-id');

      if (jobId) {
        var controller = new ProgressController(jobId);
      }
    }
);
