/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product comparison table
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    jQuery('button.add2cart').click(
      function() {
        var product = $(this).closest('form');
        var forceOptions = $(product).parent().is('.need-choose-options');
        if (forceOptions) {
          openQuickLook(product);
        }else{
          // Form AJAX-based submit
          var form = this.form;
          if (form) {
          form.commonController.submitBackground()
          }
        }
      }
    );

    jQuery('table.comparison-table tbody.data tr').not('.group').each(
      function() {
        var tr = jQuery(this);
        var td = false;
        var ident = true;
        tr.find('td').not(':first-child').each(
          function() {
            if (false === td) {
              td = jQuery(this).html();
            } else if (td != jQuery(this).html()) {
              ident = false;
            }
          }
        );
        if (ident) {
          tr.addClass('row-hidden');
        }
      }
    );

    jQuery('table.comparison-table tbody.data tr.group').each(
      function() {
        var tr = jQuery(this);
        var hide = true;
        tr.nextUntil('tr.group').each(
          function() {
            if (!jQuery(this).hasClass('row-hidden')) {
              hide = false;
            }
          }
        );
        if (hide) {
          tr.addClass('row-hidden');
        }
      }
    );

    tData = jQuery('table.comparison-table tbody.data');
    tData.addClass('diff-only');
    jQuery('input#diff').change(
      function() {
        if (jQuery(this).prop('checked')) {
          tData.addClass('diff-only');
        } else {
          tData.removeClass('diff-only');
        }
      }
    ).prop('checked', 'checked');

    jQuery('span.three-dots').mouseenter(
      function() {
        var sp = jQuery(this);
        jQuery(this).find('div').each(
          function() {
            jQuery(this).css('position', 'fixed');
            jQuery(this).css('top', sp.offset().top - jQuery(window).scrollTop() + 12);
            jQuery(this).css('left', sp.offset().left - jQuery(window).scrollLeft() + 27);
          }
        );
      }
    );

    var $table = jQuery('table.comparison-table');

    var width = 960 / Math.min(5, jQuery('tbody.header-hidden', $table).find('td').length) - 24;
    var firstColWidth = Math.min(width, $(document).width() / 2);
    width = (960 - firstColWidth) / Math.min(5, jQuery('tbody.header-hidden', $table).find('td').length - 1) - 24;
    jQuery('td', $table).width(width);
    jQuery('td:first-child', $table).width(firstColWidth);
    jQuery('tr.names td div', $table).each(function () {
      $(this).width($(this).parent().width() - 1);
    });


    var headerFixed = jQuery('tbody.header-fixed', $table);
    var header = jQuery('tbody.header', $table);

    var headerHeight = header.height();

    var hidden_td = jQuery('tbody.header-hidden td', $table);
    var headerHiddenHeight = headerFixed.height() - (hidden_td.outerHeight() - hidden_td.height());
    hidden_td.height(headerHiddenHeight);

    jQuery('tbody.header-hidden', $table).show();
    headerFixed.addClass('sticky');

    var $window = jQuery(window);
    var headerFixedTop = headerFixed.offset().top;

    $window.scroll(
      function() {
        var pageHeaderHeight = getPageHeaderHeight();
        var position = $window.scrollTop() + pageHeaderHeight;

        if (position - headerHeight > headerFixedTop) {
          headerFixed.css('top', position - headerFixedTop - 1);
          headerFixed.addClass('fixed');
        } else if (position < headerFixedTop) {
          headerFixed.css('top', headerHeight);
          headerFixed.removeClass('fixed');
        }
      }
    );
    $window.scroll();

    function getPageHeaderHeight() {
      var header;
      if (jQuery('.mobile_header:visible').length) {
        header = jQuery('.mobile_header>*');

        return header.height() + header.offset().top - $window.scrollTop();

      } else if (jQuery('.desktop-header:visible').length) {
        header = jQuery('.desktop-header');

        return header.height() + header.offset().top - $window.scrollTop();
      }

      return 0;
    }

    function getHeaderFixedTop() {
      headerFixed.removeClass('sticky');
      jQuery('tbody.header-hidden', $table).hide();

      var result = headerFixed.offset()
        ? headerFixed.offset().top
        : 0;

      jQuery('tbody.header-hidden', $table).show();
      headerFixed.addClass('sticky');

      return result
    }

    function openQuickLook(elem) {
      focusOnFirstOption();
      bindProductDetailed();

      return !popup.load(
        URLHandler.buildURL(openQuickLookParams(elem)),
        function () {
          jQuery('.formError').hide();
         },
         50000
       );
    };

    function openQuickLookParams(elem) {
      var product_id = $(elem).find('input[name=product_id]').val();

      return {
        target:      'quick_look',
        action:      '',
        product_id:  product_id,
        only_center: 1
      }
    }

    function focusOnFirstOption() {
      core.bind('afterPopupPlace', function(event, data){
        if (popup.currentPopup.box.hasClass('ctrl-customer-quicklook')) {
          var option = popup.currentPopup.box.find('.editable-attributes select, input').filter(':visible').first();
          option.focus();
         }
      });
    };

    function bindProductDetailed(){
      core.bind(
        'afterPopupPlace',
        function() {
          new ProductDetailsController(jQuery('.ui-dialog div.product-quicklook'));
        }
      );
    }
  }
);
