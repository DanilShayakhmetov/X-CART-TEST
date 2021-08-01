/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Template navigator for webmaster mode template editor
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

window.TemplateNavigator = Object.extend({
  defaults: {
    manual: false,
    tree: '[data-editor-tree]',
    switcher: '[data-editor-switcher]',
    filters: [
      function () {
        var element = this;
        return _.every(window.TemplateNavigator.nonRenderableTypes, function (type) {
          return !(element instanceof type);
        })
      }
    ]
  },

  // Parses the comment template data structure, e.g.
  //  XLite\View\SomeView : customer/view/someview.twig (19) ['layout.main' list child] {{{
  //  }}} XLite\View\SomeView : customer/view/someview.twig (19) ['layout.main' list child]
  // Match groups:
  // 1. (optional) closing tag
  // 2. widget class name
  // 3. rendered template
  // 4. template id in tree
  // 5. (optional) widget list name
  // 6. (optional) opening tag
  commentRegex: /\s*(}}})?\s*(\S*)\s:\s(\S*)\s\((\w*)\)\s(?:\['(\S*?)')?[^{]*({{{)?\s*/,

  constructor: function TemplateNavigator(base, options) {
    this.base = base;
    this.options = this.mergeOptions(this.defaults, options);

    this.setInitialState();
    this.processElementTree(document.body);
    this.setGlobalClickHandler();
  },

  processElementTree: function (element) {
    var self = this;
    setTimeout(function() {
      self.templates = _.extend(self.templates, self.buildTemplateTree(element));
    }, 0);
  },

  mergeOptions: function (source, target) {
    if (!target) {
      return source;
    }

    var result = _.clone(source);
    _.extend(result, target);

    for (var prop in result) {
      if (result[prop] instanceof Array) {
        if (prop in source && prop in target) {
          result[prop] = source[prop].concat(target[prop]);
        }
      }
    }

    return result;
  },

  setInitialState: function() {
    this.templates = {};
    var switcher = jQuery(this.options.switcher);
    var state = this.getPersistedState();
    this.enabled = state && switcher.length || this.options.manual
      ? state
      : true;
  },

  getPersistedState: function() {
    return sessionStorage.getItem('TemplateNavigator') === 'true';
  },

  setPersistedState: function(value) {
    sessionStorage.setItem('TemplateNavigator', (value ? 'true' : 'false'));
  },

  setGlobalClickHandler: function() {
    var self = this;
    $('button, a').on('click.webmasterMode', function(event) {
      if (this.enabled && self.isSuitableTarget(event.target)) {
        event.preventDefault();
      }
    })
  },

  buildTemplateTree: function(root) {
    var iterator = this.getNodeIterator(root);
    var currentNode;
    var templates = {};

    // eslint-disable-next-line no-cond-assign
    while (currentNode = iterator.nextNode()) {
      var data = this.extractNodeData(currentNode);

      if (!data || data.isClosing) {
        continue;
      }

      var nodes = this.getInnerNodes(currentNode, data.id);

      for (i = 0; i < nodes.length; i++) {
        nodes[i].template = data;
      }

      data.inner = nodes;
      templates[data.id] = data;
    }

    return templates;
  },

  getInnerNodes: function(node, id) {
    var innerNodes = [];
    var sibling;

    while (typeof(node.nextSibling) !== 'undefined' && (sibling = node.nextSibling)) {
      if (sibling.nodeType === Node.ELEMENT_NODE) {
        this.bindElementEvents(sibling);
        innerNodes.push(sibling);
      } else if (sibling.nodeType === Node.COMMENT_NODE) {
        var nextData = this.extractNodeData(sibling);
        if (nextData && nextData.isClosing && nextData.id === id) {
          break;
        }
      }

      node = node.nextSibling;
    }

    return innerNodes;
  },

  extractNodeData: function (node) {
    var matches = node.textContent.match(this.commentRegex);

    if (matches) {
      return {
        isOpening: typeof(matches[6]) !== 'undefined',
        isClosing: typeof(matches[1]) !== 'undefined',
        class: matches[2],
        template: matches[3],
        id: matches[4],
        list: matches[5],
        commentNode: node
      }
    }

    return null;
  },

  getNodeIterator: function(root) {
    var self = this;
    root = root || document.body;

    return document.createNodeIterator(
      root,
      NodeFilter.SHOW_COMMENT,
      function(node) {
        return self.commentRegex.test(node.textContent);
      }
    )
  },

  bindElementEvents: function(element) {
    var passTest = function (predicate) {
      return $(element).filter(predicate).length > 0;
    }

    if (_.every(this.options.filters, passTest)) {
      var $element = $(element);
      $element.on('mouseover', _.bind(this.onElementHover, this));
      $element.on('render_debug_layer', _.bind(this.onElementHover, this));
      $element.on('click', _.bind(this.onElementClick, this));
    }
  },

  selectTemplateInTree: function (id, openTemplateCode) {
    if (typeof(openTemplateCode) === 'undefined') {
      openTemplateCode = false;
    }

    var treeView = jQuery(this.options.tree).get(0).TreeViewController;
    treeView.selectTemplate(id, !openTemplateCode);
  },

  onElementClick: function (event) {
    if (this.enabled && this.isSuitableTarget(event.target)) {
      var id = event.currentTarget.template.id;
      this.selectTemplateInTree(id, true);

      event.preventDefault();
      event.stopImmediatePropagation();
    }
  },

  onElementHover: function (event) {
    if ((this.enabled || event.type === 'render_debug_layer') && this.isSuitableTarget(event.target)) {
      var element = event.currentTarget;
      var id = element.template.id;

      if (this.lastTarget !== event.target) {
        this.unMarkTemplate();
      }

      if (typeof(this.layers[id]) === 'undefined') {
        this.layerTrigger = event.type;
        this.layers[id] = this.renderDebugLayer(element);
        this.layerIndex++;
      }

      this.lastTarget = event.target;
    } else if (this.layerTrigger === 'mouseover') {
      this.unMarkTemplate();
    }
  },

  isSuitableTarget: function (element) {
    element = $(element);
    return $('#themetweaker-panel-loader-point').find(element).length === 0 && !element.is('#themetweaker-panel-loader-point');
  },

  renderDebugLayer: function (element) {
    var data = element.template
    var $element = $(element);

    var pos = $element.offset();
    var width = $element.outerWidth();
    var height = $element.outerHeight();

    var layer = document.body.appendChild(document.createElement('div'));

    layer.className = this.layerIndex === 0
      ? 'tpl-debug-canvas tpl-debug-current'
      : ('tpl-debug-canvas tpl-debug-' + (data.list ? 'list' : 'tpl') + '-canvas');

    $(layer).css(
      {
        top: pos.top + 'px',
        left: pos.left + 'px',
        width: width + 'px',
        height: height + 'px'
      }
    );

    return layer;
  },

  toggleEnabled: function () {
    this.enabled = !this.enabled;

    if (!this.enabled) {
      this.unMarkTemplate();
    }

    this.setPersistedState(this.enabled);
  },

  markTemplateById: function (id) {
    if (this.templates && this.templates[id]) {
      var data = this.templates[id];
      if (data.inner.length > 0) {
        data.inner[0].fireEvent('render_debug_layer', 'CustomEvent');
      }
    }
  },

  unMarkTemplate: function () {
    this.layerTrigger = null;
    $('.tpl-debug-canvas').remove();
    this.layers = {};
    this.layerIndex = 0;
  }
})

window.TemplateNavigator.nonRenderableTypes = [
  HTMLTitleElement,
  HTMLStyleElement,
  HTMLMetaElement,
  HTMLLinkElement,
  HTMLScriptElement
];