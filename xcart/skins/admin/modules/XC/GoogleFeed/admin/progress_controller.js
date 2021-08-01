/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Calculate quick data controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
jQuery().ready(
  function() {
    jQuery('.generation-progress .bar')
      .bind(
        'error',
        function() {
          this.errorState = true;
          self.location = URLHandler.buildURL({ 'target': 'google_feed', 'generation_failed': 1 });
        }
      )
      .bind(
        'complete',
        function() {
          if (!this.errorState) {
            self.location = URLHandler.buildURL({ 'target': 'google_feed', 'generation_completed': 1 });
          }
        }
      ).bind(
        'cancel',
        function() {
          setTimeout(function() {
            self.location = URLHandler.buildURL({ 'target': 'google_feed' });
          }, 4000);
        }
      );

    var height = 0;
    jQuery('.generation-completed .files.std ul li.file').each(
      function () {
        height += jQuery(this).outerHeight();
      }
    );

    var bracket = jQuery('.generation-completed .files ul li.sum .bracket');
    var diff = bracket.outerHeight() - bracket.innerHeight();

    bracket.height(height - diff);
  }
);
