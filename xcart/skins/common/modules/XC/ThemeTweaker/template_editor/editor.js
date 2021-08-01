/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Templates debugger
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(
  function() {
    var treeView = new TreeView('.themeTweaker_tree');
    var interface = jQuery('.themeTweaker_tree').data('interface');
    var innerInterface = jQuery('.themeTweaker_tree').data('inner-interface');
    var templateNavigator = new TemplateNavigator('.themeTweaker_tree');

    jQuery(window).on('reload', function () {
      assignShadeOverlay(treeView.container)
    });

    jQuery('#themeTweaker_wrapper').resizable(
      {
        resize: function (event, ui) {
          // Change left margin for preview area
          jQuery('body #main .notification_editor').css('margin-left', jQuery('#themeTweaker_wrapper').outerWidth() + 'px');
        }
      }
    ).show();

    var tree = jQuery('.themeTweaker_tree');

    tree.on('select_node.jstree', function (event, data) {
      if (!treeView.preventEdit) {
        var url = URLHandler.buildURL({
          target: 'theme_tweaker_template',
          template: data.node.data.templatePath,
          interface: interface,
          innerInterface: innerInterface
        });

        var wnd = window.open(url, 'TTEditor', 'width=1050px,height=550px,menubar=no,toolbar=no,location=no,directories=no,status=no');
        wnd.focus();
      }
    });

    tree.on('hover_node.jstree', function (event, data) {
      templateNavigator.markTemplateById(data.node.data.templateId);
    });

    tree.on('dehover_node.jstree', function (event, data) {
      templateNavigator.unMarkTemplate();
    });

    var controlPanel = jQuery('#themeTweaker_wrapper .themeTweaker-control-panel');
    switcher = jQuery('#themeTweaker-switcher', controlPanel).change(function (event) {
      templateNavigator.toggleEnabled();
    });
    switcher.prop('checked', templateNavigator.enabled);

    tree.prepend(controlPanel);

    // Initialize left margin for preview area
    jQuery('body #main .notification_editor').css('margin-left', jQuery('#themeTweaker_wrapper').outerWidth() + 'px');
  }
);
