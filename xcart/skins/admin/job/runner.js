/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var BouncingRunner = Object.extend({
  endpoint:         '',
  requestTimeout:   70000,
  intervalRate:     10000,
  serviceName:      'default',

  waiting:    false,

  constructor: function JobsRunner(endpoint, options) {
    this.endpoint = endpoint;
    this.serviceName = options['serviceName'] || this.serviceName;
    this.requestTimeout = options['requestTimeout'] || this.requestTimeout;
    this.intervalRate = options['intervalRate'] || this.intervalRate;
    this.intervalId = null;

    core.trigger('backendRunner.initialized', this);
  },

  setEndpoint: function(endpoint) {
    this.endpoint = endpoint;
  },

  start: function() {
    this._makeRequest();

    if (!this.intervalId) {
      this.intervalId = setInterval(
          _.bind(this._makeRequest, this),
          this.intervalRate
      );
    }
  },

  stop: function() {
    clearInterval(this.intervalId);
    this.intervalId = null;
  },

  _makeRequest: function() {
    if (this.waiting === true) {
      return;
    }

    this.waiting = true;

    core.get(this.endpoint, _.bind(this._requestHandler, this), {}, {timeout: this.requestTimeout});
  },

  _requestHandler: function(xhr, status, data) {
    if (xhr.readyState != 4 || xhr.status != 200) {
      core.trigger('backendRunner.error', {
        'runner': this,
        'data':   data
      });

    } else {
      core.trigger('backendRunner.success', {
        'runner': this,
        'data':   jQuery.parseJSON(data)
      });
      this.waiting = false;
    }
  },

});

core.bind('backendRunner.run', function(event, data) {
  new BouncingRunner(data['endpoint'], data || {});
});

core.trigger('bouncingRunner.ready');
