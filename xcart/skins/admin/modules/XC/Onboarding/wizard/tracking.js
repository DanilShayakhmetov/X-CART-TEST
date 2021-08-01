/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Wizard tracking module
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/tracking', [], function () {
  return Object.extend({
    constructor: function OnboardingTracking() {

    },

    sendEvent: function (event) {
      if (!event.type || !event.name) {
        console.error('[OnboardingTracking] No event type or name given');
        return false;
      }

      if (!_.has(this.events, event.type)) {
        console.error('[OnboardingTracking] Unknown event type - ' + event.type);
        return false;
      }

      var template = this.events[event.type];

      this.pushEvent({
        type: template.type,
        name: template.name,
        intercomName: template.name + ' ' + event.name,
        props: _.extend(this.commonProperties(), template.props(event), event.args),
        options: this.commonOptions()
      });
    },

    pushEvent: function(event) {
      var mixpanel = {
        type: event.type,
        arguments: [event.name, event.props, _.extend({}, event.options, {integrations: {All: true, Intercom: false}})]
      };
      var intercom = {
        type: event.type,
        arguments: [event.intercomName, event.props, _.extend({}, event.options, {integrations: {All: false, Intercom: true}})]
      };
      core.trigger('concierge.push', { list: [mixpanel, intercom] });
    },

    events: {
      page: {
        type: 'page',
        name: 'Loaded a page',
        props: function(data) {
          return {
            'Page Name': 'Concierge: ' + data.name,
            title: data.name,
            onboarding_step: data.step
          }
        }
      },
      form: {
        type: 'track',
        name: 'Submitted Form',
        props: function(data) {
          var formData = data.form || {};
          return _.extend({
            'Form Name': 'Concierge: ' + data.name,
            onboarding_step: data.step
          }, formData);
        }
      },
      link: {
        type: 'track',
        name: 'Clicked link',
        props: function(data) {
          return {
            'Link Name': 'Concierge: ' + data.name,
            title: data.name,
            onboarding_step: data.step
          }
        }
      }
    },

    commonProperties: function() {
      return {
        'host'       : this.getHost(),
        'EventSource': 'Concierge',
      };
    },

    commonOptions: function() {
      return {
        context: {}
      };
    },

    getHost: function() {
      return xliteConfig.base_url;
    }
  });
});