/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Slidebar
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('wizard/loader', ['js/vue/vue', 'ready'], function (XLiteVue) {
  Vue.directive('data', {
    update: function() {
      var object = JSON.parse(this.expression);
      for (var key in object) {
        this.vm.$set(key, object[key]);
      }
    },
  });

  if (core.isDeveloperMode) {
    Vue.config.debug = true;
    Vue.config.devtools = true;
  }

  if (core.isCloud) {
    Vue.config.cloud = true
  }
});

define('wizard/controller', ['js/vue/vue', 'wizard/store', 'wizard/tracking'], function (XLiteVue, Store, Tracking) {
  var tracking = new Tracking();

  XLiteVue.component('xlite-onboarding-wizard', {
    props: {
      step: String,
      steps: Object,
      state: String,
      lastProduct: String
    },
    mixins: [VueLoadableMixin],
    store: Store,

    activate: function (done) {
      done();
    },

    ready: function() {
      jQuery(window).resize(_.bind(this.calculateViewport, this));
      core.bind('recalculateLeftMenuPosition', _.bind(this.calculateViewport, this));
      if (this.lastProduct) {
        this.updateProduct(this.lastProduct);
      }
      let currentStep = this.accessCurrentProgress() || this.step;
      if (this.steps[currentStep] === undefined) {
        currentStep = 'intro';
      }
      this.currentStep = currentStep;
      $('[data-toggle="tooltip"]', this.$el).tooltip();
      core.trigger('wizard.ready', this);
      this.calculateViewport();
    },

    data: function () {
      return {
        currentStep: null,
        direction: 'forward',
        progress: 0,
        successSteps: Array,
        stepAliases: new Map([
          ['add_product', 'product'],
          ['product_added', 'product'],
          ['add_product_cloud', 'product'],
          ['product_added_cloud', 'product'],
          ['company_logo', 'company'],
          ['company_logo_added', 'company'],
          ['location', 'location'],
          ['company_info', 'location'],
          ['shipping', 'shipping'],
          ['shipping_rates', 'shipping'],
          ['shipping_done', 'shipping'],
          ['payment', 'payment']
        ])
      }
    },

    vuex: {
      actions: {
        updateProduct: function(state, id) {
          state.dispatch('UPDATE_PRODUCT', id);
        },
        updateProductName: function(state, id) {
          state.dispatch('UPDATE_PRODUCT_NAME', id);
        },
        updateProductImage: function(state, id) {
          state.dispatch('UPDATE_PRODUCT_IMAGE', id);
        },
      }
    },

    computed: {
      headerTrigger: function () {
        return this.currentStep !== 'intro';
      },
      headerClasses: function() {
        var result = {
          'header-intro-transition': true
        };

        if (this.headerTrigger) {
          result['header-intro-leave'] = true;
        }

        return result;
      },
      classes: function () {
        var result = {};
        result['current-step-' + this.currentStep] = true;
        return result;
      },
      stepTransition: function () {
        return 'step-' + this.transitionType + '-' + this.direction;
      },
      transitionType: function() {
        if (this.currentStep == 'intro' ||
          (this.currentStep == 'add_product' && this.direction == 'forward')) {
          return 'fade';
        }

        return 'slide';
      },
    },

    watch: {
    },

    transitions: {
    },

    events: {
      blockBody: function() {
        jQuery('#main').addClass('reloading reloading-circles');
      },

      unblockBody: function() {
        jQuery('#main').removeClass('reloading reloading-circles');
      },

      'wizard.tracking.event': function(type, name, data) {
        data = data || {};
        name = name || '';
        var event = {
          type: type,
          name: 'Onboarding Step ' + this.steps[this.currentStep].name + ' ' + name,
          step: this.currentStep,
          args: data
        };
        tracking.sendEvent(event);
      },

      'wizard.landmark.pass': function(name) {
        this.$broadcast('wizard.landmark.pass', name);
      },

      'wizard.step.switch': function(name, affectProgress, check = true) {
        this.goToStepByName(name, affectProgress, check);
      },

      'wizard.step.requestNext': function() {
        this.goToNextStep();
      },

      'wizard.step.requestPrevious': function() {
        this.goToPreviousStep();
      },

      'wizard.hide': function () {
        this.hideWizard();
      }

      ,'wizard.close': function () {
        this.closeWizard();
      }
    },

    methods: {
      calculateViewport: function() {
        $(this.$el).addClass('onboarding-wizard--initial');
        var height = $('#main').height();
        var width = $('#main').width();
        height = height < 630 ? 630 : height;
        $(this.$el).height(height);
        $(this.$el).css('max-width', width);
        $(this.$el).find('.onboarding-wizard--body').css('max-height', height);
        $(this.$el).removeClass('onboarding-wizard--initial');
      },
      checkStepOnSucceed: function (name) {
        this.successSteps = []
        try {
          this.successSteps = JSON.parse(jQuery.cookie('Wizard_landmarks') ? jQuery.cookie('Wizard_landmarks') : 'null')
          if (this.successSteps) {
            return this.successSteps.includes(this.stepAliases.get(name))
          } else {
            return false
          }
        } catch (e) {
          console.log('Cookie parse error: ' + e.message)
          return false
        }
      },
      goToNextStep: function () {
        this.goToStepByDirection('forward', true);
      },
      goToPreviousStep: function () {
        this.goToStepByDirection('backward');
      },
      goToStepByDirection: function(direction, affectProgress) {
        var stepNames = Object.keys(this.steps);
        var currentIndex = _.indexOf(stepNames, this.currentStep);

        var next = direction === 'forward'
          ? currentIndex + 1
          : currentIndex - 1;

        var step = stepNames[next]
          ? stepNames[next]
          : null;

        this.direction = direction;
        this._switchStep(step, affectProgress);

        if (this.checkStepOnSucceed(step)) {
          this.currentStep = step
          this.goToNextStep()
        }
      },
      goToStepByName: function(name, affectProgress, check) {
        var stepNames = Object.keys(this.steps);
        var currentIndex = _.indexOf(stepNames, this.currentStep);
        var newIndex = _.indexOf(stepNames, name);

        if (currentIndex > newIndex) {
          this.direction = 'backward';
        } else {
          this.direction = 'forward';
        }

        this._switchStep(name, affectProgress);

        if (check && this.checkStepOnSucceed(name)) {
          this.currentStep = name
          this.goToNextStep()
        }
      },
      _switchStep: function (step, affectProgress) {
        var self = this;

        if (_.has(this.steps, step)) {
          this.$nextTick(function() {
            core.trigger('wizard.step.changed', step);
            self.persistCurrentProgress(step);

            if (affectProgress) {
              self.$broadcast('wizard.progress.change', self.steps[step], step);
            }

            self.currentStep = step;
            self.$dispatch('wizard.tracking.event', 'page');
          })
        } else {
          console.error('[wizard] There is no step with name "' + step +'"');
        }
      },
      closeWizard: function() {
        this.$emit('blockBody');
        this.$emit('wizard.tracking.event', 'link', 'Wizard finished');

        window.location = URLHandler.buildURL({'target': 'main'});
      },
      hideWizard: function() {
        this.$emit('blockBody');
        this.$emit('wizard.tracking.event', 'link', 'Wizard closed');

        if (this.state !== 'minimized') {
          var data = {};
          data[xliteConfig.form_id_name] = xliteConfig.form_id;

          core.post({
            target: 'onboarding_wizard',
            action: 'minimize_wizard'
          }, null, data);
        } else {
          window.location = URLHandler.buildURL({'target': 'main'});
        }
      },
      disableWizard: function() {
        this.$emit('blockBody');
        this.$emit('wizard.tracking.event', 'link', 'Wizard disabled');

        var data = {};
        data[xliteConfig.form_id_name] = xliteConfig.form_id;
        core.post({
          target: 'onboarding_wizard',
          action: 'disable_wizard'
        }, null, data);
      },
      isCurrentStep: function (name) {
        return this.currentStep === name;
      },
      accessCurrentProgress: function() {
        var step = jQuery.cookie('Wizard_currentProgress');

        if (step === 'add_product' && this.lastProduct) {
          this.$dispatch('wizard.landmark.pass', 'product');
          return 'product_added';
        }

        if (step === 'company_logo_added') {
          return 'company_logo';
        }

        return step;
      },
      persistCurrentProgress: function(stepName) {
        jQuery.cookie('Wizard_currentProgress', stepName);
      },
      browseTools: function () {
        this.$dispatch('wizard.tracking.event', 'link', 'Browse Top Trending Tools')
        window.open('service.php#/marketplace')
      }
    }
  });
});
