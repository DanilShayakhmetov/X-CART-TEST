/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Wizard progress widget component
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/progress', ['js/vue/vue'], function (XLiteVue) {
  XLiteVue.component('xlite-wizard-progress', {
    props: ['currentStep', 'step', 'steps', 'landmarks', 'lastProduct'],

    activate: function (done) {
      done();
    },

    ready: function() {
      this.$bar = $(this.$el).find('.progress-line-filled');
      jQuery(window).on('popstate', _.bind(this.setIndexFromUrl, this));
      core.bind('wizard.step.changed', _.bind(this.stepChanged, this));
      this.setIndexFromUrl();
    },

    data: function () {
      return {
        switchBlocked: false,
        progress: 0,
        previousLandmarkClass: null,
        previousProgress: 0,
        pushState: true,
      }
    },

    vuex: {
      getters: {
      },

      actions: {
      }
    },

    computed: {
      barStyle: function () {
        return {
          width: this.progress + '%'
        }
      },
      finishClass: function() {
        return {
          active: this.progress === 100
        }
      },
      landmarkClass: function () {
        var self = this;
        var classes = _.reduce(this.landmarks, function(result, item) {
          result[item.index] = {};

          if (self.isLandmarkOfCurrentStep(item)) {
            result[item.index]['current'] = true;
            if (self.previousProgress !== self.progress && self.previousLandmarkClass && !self.previousLandmarkClass[item.index]['current']) {
              result[item.index]['current-delay'] = true;
            }
          } else if (item.activeOn && item.activeOn <= self.progress) {
            result[item.index]['active'] = true;
            if (self.previousProgress !== self.progress && self.previousLandmarkClass && !self.previousLandmarkClass[item.index]['active']) {
              result[item.index]['active-recent'] = true;
            }
          }

          if (_.contains(self.accessLandmarks(), item.index)) {
            result[item.index]['finished'] = true;
            if (self.previousProgress !== self.progress && self.previousLandmarkClass && !self.previousLandmarkClass[item.index]['finished-delay']) {
              result[item.index]['finished-delay'] = true;
            }
          }

          if (self.canSwitchToStep(item.index)) {
            result[item.index]['switchable'] = true;
          }

          return result;
        }, {});

        _.delay(self.clearDelay, 500);
        return classes;
      }
    },

    watch: {
    },

    transitions: {
    },

    events: {
      'wizard.landmark.pass': function(name) {
        this.addLandmark(name);
      },
      'wizard.progress.change': function(data, step) {
        var self = this;
        if (data.progress >= 0 && data.progress <= 100 && data.progress > this.progress) {
          this.previousLandmarkClass = this.landmarkClass;
          this.previousProgress = this.progress;
          this.progress = data.progress;
          this.persistMaxProgress(step);
        }
      }
    },

    methods: {
      accessLandmarks: function() {
        var data = [];
        try {
          data = JSON.parse(jQuery.cookie('Wizard_landmarks'));
        } catch (e) {}
        return data;
      },
      addLandmark: function(name) {
        var current = this.accessLandmarks();
        current.push(name);

        jQuery.cookie('Wizard_landmarks', JSON.stringify(current));
      },
      assignBarDelay: function() {
        var condition = _.some(this.landmarkClass, function(item) {
          return _.has(item, 'active-recent');
        });

        if (condition) {
          this.$bar.addClass('delay');
        }
      },
      clearDelay: function() {
        $(this.$el).find('.landmark').removeClass('current-delay active-delay finished-delay');
        this.$bar.removeClass('delay');
      },
      isLandmarkOfCurrentStep: function(item) {
        return _.contains(item.steps, this.currentStep);
      },
      goToStep: function(index) {
        var self = this;

        if (this.canSwitchToStep(index) && !this.switchBlocked) {
          this.switchBlocked = true;
          this.$dispatch('wizard.step.switch', this.getTargetByIndex(index), false, false);

          _.delay(function() {
            self.switchBlocked = false;
          }, 700);
        }
      },
      goToNextStep: function(index) {
        this.$dispatch('wizard.step.requestNext');
      },
      canSwitchToStep: function(index) {
        if (typeof(this.landmarks[index]) === 'undefined') {
          return false;
        }
        var activeOn = this.landmarks[index].activeOn;
        return this.progress > 0 && this.progress >= activeOn;
      },
      getTargetByIndex: function(index) {
        if (typeof(this.landmarks[index]) === 'undefined') {
          return false;
        }
        var target = this.landmarks[index].target;

        if (target === 'add_product' && (this.lastProduct || _.contains(this.accessLandmarks(), 'product'))) {
          target = 'product_added';
        } else if (target === 'add_product_cloud' && (this.lastProduct || _.contains(this.accessLandmarks(), 'product'))) {
          target = 'product_added_cloud';
        }

        return target;
      },
      isCurrentStep: function (name) {
        return this.currentStep === name;
      },
      persistMaxProgress: function(stepName) {
        jQuery.cookie('Wizard_maxProgress', stepName);
      },
      getIndexByTarget: function(target) {
        const targets = [];
        jQuery.each(this.landmarks, function (index, item) {
          item.steps.map(function(step) {
            targets[step] = index;
          })
          targets[item.target] = index;
        })

        return targets[target];
      },
      stepChanged: function(ev, stepName) {
        if (this.pushState) {
          this.changeUrl(this.getIndexByTarget(stepName));
        } else {
          this.pushState = true;
        }
      },
      changeUrl: function(stepName) {
        const currentStep = this.getIndexFromUrl();
        if (stepName && currentStep !== stepName) {
          const url = document.location.pathname + document.location.search + '#step=' + stepName;
          window.history.pushState(null, null, url);
        }
      },
      getIndexFromUrl: function() {
        const match = document.location.hash.match(/^#step=([a-z_]*)$/);
        return match ? match[1] : null;
      },
      setIndexFromUrl: function() {
        const index = this.getIndexFromUrl();
        if (index) {
          this.pushState = false;
          this.goToStep(index);
        }
      }
    }
  });
});
