/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Email input handler
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
    {
      pattern: 'input#roles',
      canApply: function () {
        return this.$element.is('#roles');
      },
      handler: function () {
        var $el = this.$element;
        var rootValue = this.$element.data('root-value');
        var comment = $el.closest('.roles-value').find('.roles-comment');

        this.$element.change(function() {
          var value = $el.val();

          if (value && value.indexOf(String(rootValue)) !== -1) {
            comment.show();
          } else {
            comment.hide();
          }
        });
      }
    }
);
