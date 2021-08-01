/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Apply attribute values globally controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery().ready(
  function() {
    jQuery('.apply-attribute-values-progress .bar')
      .bind(
        'error',
        function() {
          this.errorState = true;
          self.location = URLHandler.buildURL(
            {
              'target': 'product',
              'product_id': core.getURLParam('product_id'),
              'page': core.getURLParam('page'),
              'spage': 'global',
              'apply_attr_values_failed': 1
            }
            );
        }
      )
      .bind(
        'complete',
        function() {
          if (!this.errorState) {
            self.location = URLHandler.buildURL(
              {
                'target': 'product',
                'product_id': core.getURLParam('product_id'),
                'page': core.getURLParam('page'),
                'spage': 'global',
                'apply_attr_values_completed': 1
              }
            );
          }
        }
      ).bind(
        'cancel',
        function() {
          setTimeout(function() {
            self.location = URLHandler.buildURL(
              {
                'target': 'product',
                'product_id': core.getURLParam('product_id'),
                'page': core.getURLParam('page'),
                'spage': 'global',
              }
            );
          }, 4000);
        }
      );
  }
);
