/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Login link
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {

    // Open login link is popup
    core.microhandlers.add(
      'loginPopup',
      'a.log-in',
      function() {

        jQuery(this).click(
          function(event) {
            loadDialogByLink(
              event.currentTarget,
              URLHandler.buildURL({
                'target':  'login',
                'widget':  '\\XLite\\View\\Authorization',
                'popup':   1,
                'fromURL': jQuery(this).data('return-url') || (self.location + ''),
                'login':   jQuery(event.currentTarget).data('login') ? jQuery(event.currentTarget).data('login') : ''
              }),
              {width: 'auto'},
              null,
              this
            );

            return false;
          }
        )
      }
    );

    var initiateTimer = function(base) {
      var timeLeft = base.data('time-left');
      if (timeLeft) {
        (function() {
          timeLeft--;
          if (0 < timeLeft) {
            var min = parseInt(timeLeft / 60);
            var sec = timeLeft % 60;
            jQuery('#timer', base).text((10 > min ? '0' : '') + min +  ':' + (10 > sec ? '0' : '') + sec);
            setTimeout(arguments.callee, 1000);

          } else {
            jQuery(base).removeClass('locked-out');
          }
        })()
      }
    };

    initiateTimer(jQuery('.login-form'));

    // Login popup form
    core.microhandlers.add(
      'loginPopupForm',
      'form.login-form',
      function(event) {
        this.commonController.enableBackgroundSubmit();
        var form = this.commonController.$form;
        var lockoutBase = form.find('.login-form');

        core.bind(
            'login.lockout',
            function(event, data) {
              lockoutBase.addClass('locked-out');
              lockoutBase.data('time-left', data.time);
              initiateTimer(lockoutBase);
            }
        );

        initiateTimer(lockoutBase);

        form.find('a.forgot').click(
          function(event) {
            loadDialogByLink(
              event.currentTarget,
              URLHandler.buildURL({
                'target':  'recover_password',
                'widget':  '\\XLite\\View\\RecoverPassword',
                'popup':   1,
                'fromURL': (self.location + ''),
                'email':   form.find('#login').val() ? form.find('#login').val() : ''
              }),
              {width: 'auto'},
              null,
              this
            );

            return false;
          }
        );

        var f = this.commonController.getErrorPlace;
        this.commonController.getErrorPlace = function()
        {
          if (!this.errorPlace) {
            var box = f.apply(this);
            box
              .remove()
              .insertBefore(this.$form.find('button[type="submit"]').eq(0));
          }

          return this.errorPlace;
        }

        core.bind(
          'recoverPasswordSent',
          function() {
            popup.close();
          }
        );
      }
    );

    // Recovery password popup form
    core.microhandlers.add(
      'recoverPasswordPopupForm',
      'form.recovery-form',
      function(event) {
        if (jQuery(this).parents('.popup-window-entry').length) {
          this.commonController.enableBackgroundSubmit();
        }
      }
    );
  }
);

