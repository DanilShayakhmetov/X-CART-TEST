/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * place-order.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define(
  'checkout_fastlane/sections/payment/place_order',
 ['vue/vue',
  'checkout_fastlane/sections/section_change_button'],
  function(Vue, SectionChangeButton) {

  var PlaceOrder = Vue.extend({
    name: 'place-order',
    replace: false,

    vuex: {
      getters: {
        sections_ready: function(state) {
          return _.values(state.sections.list).every(function(section) {
            return section.complete;
          });
        },
        is_visible: function(state) {
          return state.sections.current && state.sections.current.name === 'payment';
        },
        total_text: function(state) {
          return state.order.total_text;
        },
        notes: function(state) {
          return state.order.notes;
        },
        paymentData: function(state) {
          return state.order.paymentData;
        }
      },
    },

    data: function() {
      return {
        blocked: false,
        blockers: [],
        isFormValid: null,
        endpoint: {
          target: 'checkout',
          action: 'checkout'
        },
        request_options: {
          timeout: '30000'
        }
      };
    },

    ready: function() {
      this.form = $('form.place')
      new CommonForm(this.form);
      this.assignHandlers();
      this.validateForm();
    },

    methods: {
      assignOpenTermsHandler: _.once(function() {
        $('.terms-notice a').click(_.bind(this.openTerms, this));
      }),

      assignHandlers: function() {
        core.bind('checkout.common.block', _.bind(
          function() {
            this.blocked = true;
          }, this)
        );

        core.bind('checkout.common.unblock', _.bind(
          function() {
            this.blocked = false;
          }, this)
        );

        this.form.get(0).commonController
          .enableBackgroundSubmit();

        this.form.change(this.validateForm.bind(this));
        this.form.bind('beforeSubmit', _.bind(this.fillForm, this));
        this.form.bind('afterSubmit', _.bind(this.afterSubmitPlaceForm, this));

        this.assignOpenTermsHandler();
      },

      fillForm: function(event) {
        this.$root.startLoadAnimation(core.t('Transaction is in progress'));
        var form = $('form.place');

        if (form.find('.collected-params').length) {
          form.find('.collected-params').remove();
        }

        var params = $('<div></div>').addClass('collected-params');
        form.append(params);

        $.each(this.getData(), function (k, v) {
          params.append($('<input type=hidden />').attr('name', k).val(v));
        });

        $('.payment-tpl :input').filter(
          function () {
            return this.checked
              || (
                this.type != 'checkbox'
                && this.type != 'radio'
              );
          }).each(function () {
            params.append($('<input type=hidden />').attr('name', $(this).attr('name')).val($(this).val()));
          });
      },

      afterSubmitPlaceForm: function(event, args) {
        this.$root.finishLoadAnimation();
        if (args.textStatus === 'success' && args.isValid) {
          this.onSuccess(args.data, args.XMLHttpRequest.status, args.XMLHttpRequest);
        }
      },

      placeOrder: function() {
        this.validatePaymentForm();
        var root = this.$root;
        if (this.ready) {
          root.startLoadAnimation();
          _.defer(
            _.bind(function() {
              this.onReadyPlaceOrder();
              if (this.state.state !== false) {
                this.sendForm();
              } else {
                root.finishLoadAnimation();
              }
            }, this)
          );
        } else {
          this.onNotReadyPlaceOrder();
        }
      },

      onReadyPlaceOrder: function() {
        this.blocked = true;
        this.state = {widget: this, state: this.ready};
        core.trigger('checkout.common.ready', this.state);
      },

      onNotReadyPlaceOrder: function() {
        core.trigger('checkout.common.nonready', this);
      },

      validateForm: function () {
        this.isFormValid = this.formController
          && this.formController.validate({
            silent: !this.formController.isChanged(true) || !this.formController.wasFilledOnce(),
            focus: false
          });
      },

      validatePaymentForm: function () {
        var paymentFormController = document.querySelector('form.payment-form') ? document.querySelector('form.payment-form').commonController : null;

        this.isFormValid = paymentFormController
          && paymentFormController.validate({
            silent: false,
            focus: false
          });
      },

      sendForm: function() {
        this.form.submit();
      },

      getData: function() {
        var data = {
          notes: this.notes
        };

        data = _.extend(data, this.paymentData);

        return data;
      },

      onSuccess: function(response, status, xhr){
        if (response.length > 0
          && xhr.getResponseHeader('Content-Type').match(/^text\/html/i)
          && response.match(/body[\s\S]*? onLoad=/i)
        ) {
          // form based
          document.write(response);
          emitEvent(window, 'load');
        } else if (xhr.getResponseHeader('AJAX-Location')) {
          var url = xhr.getResponseHeader('AJAX-Location');
          if (url) {
            self.location = url;
          } else {
            self.location.reload(true);
          }
        }
      },

      onFail: function() {
        core.showError('Error on order place.');
        this.$root.finishLoadAnimation();
      },

      openTerms: function(event) {
        event.preventDefault();
        return !popup.load(
          event.currentTarget,
          {
            dialogClass: 'terms-popup'
          }
        );
      }
    },

    watch: {
      ready: function (newValue) {
        if (!newValue) {
          core.trigger('checkout.common.state.nonready', {});
        } else if (this.blocked || !_.isEmpty(this.blockers)) {
          core.trigger('checkout.common.state.blocked', {});
        } else {
          core.trigger('checkout.common.state.ready', {});
        }
      },
      is_visible: function (newValue) {
        if (newValue) {
          this.validateForm();
        }
      }
    },

    events: {
      reloadingBlock: function(sender) {
        this.blockers.push(sender);
      },
      reloadingUnblock: function(sender) {
        this.blockers.pop();
      },
    },

    computed: {
      formController: function() {
        return document.querySelector('form.place') ? document.querySelector('form.place').commonController : null;
      },
      ready: {
        cache: false,
        get: function() {
          return this.sections_ready
            && _.isEmpty(this.blockers)
            && this.isFormValid
        }
      },
      label: function() {
        return core.t('Place order');
      },
      classes: function() {
        return {
          'disabled': !this.ready || this.blocked || !_.isEmpty(this.blockers),
        }
      },
      btnTitle: function() {
        return !this.ready
            ? core.t("Order cannot be placed because some steps are not completed")
            : core.t("Click to finish your order");
      }
    },
  });

  Vue.registerComponent(SectionChangeButton, PlaceOrder);

  return PlaceOrder;
});
