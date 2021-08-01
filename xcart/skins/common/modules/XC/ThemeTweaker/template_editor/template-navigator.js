/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Template navigator for webmaster mode template editor
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function yieldingLoop(array, chunksize, callback, finished) {
    var i = 0;
    (function chunk() {
        var end = Math.min(i + chunksize, array.length);
        for ( ; i < end; ++i) {
            callback.call(array, i);
        }
        if (i < array.length) {
            setTimeout(chunk, 0);
        } else {
            finished.call(array);
        }
    })();
};

var TemplateNavigator = function (base, options) {
    this.base = base;
    this.options = this.mergeOptions(this.defaults, options);

    var switcher = jQuery(this.options.switcher);

    this.enabled = jQuery.cookie('TemplateNavigator') && switcher.length || this.options.manual ? ('1' === jQuery.cookie('TemplateNavigator')) : true;

    this.templates = [];
    this.current = null;
    this.elements = jQuery();

    this.processHtmlElements('*');
};

TemplateNavigator.prototype.processHtmlElements = function(selector) {
    var selection = jQuery(selector);

    var elements = this.applyFilters(selection, this.options.filters);

    elements.mousemove(
        function (event) {
            if (self.enabled) {
                self.markTemplate(this, event);
            }
        }
    );

    var self = this;

    jQuery(document).on('mousemove', 'body',
        function (event) {
            if (!jQuery('[data-editor-tree]').find(event.target).length) {
                self.checkTemplateRegion(event);
            }
        }
    );

    this.mapElements(elements, this.enabled);

    this.elements = this.elements.add(elements);
};

TemplateNavigator.prototype.mergeOptions = function(source, target) {
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
};

TemplateNavigator.prototype.applyFilters = function(elements, filters) {
    if (!(elements instanceof jQuery)) {
        elements = jQuery(elements);
    }

    return filters.reduce(function(elements, filter) {
        return elements.filter(filter);
    }, elements);
};

TemplateNavigator.prototype.defaults = {
    manual: false,
    tree: '[data-editor-tree]',
    switcher: '[data-editor-switcher]',

    filters: [
        function() {
            return this.nodeType == 1;
        },
        function() {
            return !this.innerHTML || this.innerHTML.search(/<[a-z]/) == -1;
        }
    ]
};

TemplateNavigator.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled;

    if (!this.enabled) {
        this.unMarkTemplate();
    }

    jQuery.cookie('TemplateNavigator', this.enabled ? '1' : '0');
};

TemplateNavigator.prototype.mapElements = function (elements, shadeElements) {
    if (
        typeof elements == 'undefined'
        || !elements.length
    ) {
        return false;
    }
    shadeElements = shadeElements || false;

    var self = this;
    if (shadeElements) {
        core.shadeWidgetsCollection('body');
    }

    yieldingLoop(
        elements.toArray(),
        5,
        function(index) { self.mapElement(this[index]) },
        _.partial(self.onMapElementsEnd, shadeElements)
    );
};

TemplateNavigator.prototype.onMapElementsEnd = function(shadeElements) {
    this.length = 0;
    if (shadeElements) {
        core.unshadeWidgetsCollection('body');
    }
};

TemplateNavigator.prototype.mapElement = function (element) {
    element = jQuery(element).get(0);

    if (typeof element == 'undefined') {
        return false;
    }

    element.branch = this.getTemplatesBranch(element);
};

TemplateNavigator.prototype.getTemplatesBranch = function (element) {
    var branch = [];
    var leaf = null;

    while (element && element.parentNode) {
        leaf = this.getTemplate(element);

        if (leaf) {
            branch = branch.concat(leaf);
        }

        element = element.parentNode;
    }

    return branch;
};

TemplateNavigator.prototype.getTemplate = function (element) {
    var first = this.getFirst(element);
    var last = this.getLast(element, first);

    if (!first || !last) {
        return false;
    }

    var data = first.data.match(/\s+(\S+)\s:\s(\S+)?\s\((\w+)\)\s(?:\['(\S+)')?/);

    if (!data) {
        return false;
    }

    var result = {
        begin: jQuery(this.getNextVisibleElement(first)),
        end:   jQuery(this.getPreviousVisibleElement(last)),
        class: data[1],
        tpl:   data[2] || 'n/a',
        id:    data[3],
        list:  false
    };

    if (data[2]) {
        this.addTemplateElement(data[3], result);
    }

    var list = null;
    if (data[4]) {
        list = this.getListTemplate(first, data[4]);
    }

    if (list) {
        result = [result];
        result.push(list);
    }

    return result;
};

TemplateNavigator.prototype.getFirst = function (element) {
    var first = null;

    while (element.previousSibling) {
        element = element.previousSibling;
        if (element.nodeType == 8 && element.data.search(' {{{ ') != -1) {
            first = element;

            break;
        }
    }

    return first;
};

TemplateNavigator.prototype.getLast = function (element, first) {
    var last = null;

    if (!first) {
        return null;
    }

    var match = first.data.match(/ \((\w+)\)/);
    var lastPattern = new RegExp(' \}\}\} .+\(' +  match[1] + '\)');

    while (element.nextSibling) {
        element = element.nextSibling;
        if (element.nodeType == 8 && element.data.search(lastPattern) != -1) {
            last = element;

            break;
        }
    }

    return last;
};

TemplateNavigator.prototype.getNextVisibleElement = function (element) {
    while (element.nextElementSibling) {
        element = element.nextElementSibling;
        if (element && this.isVisible(element)) {
            break;
        }
    }

    return element;
};

TemplateNavigator.prototype.getPreviousVisibleElement = function (element) {
    while (element.previousElementSibling) {
        element = element.previousElementSibling;
        if (element && this.isVisible(element)) {
            break;
        }
    }

    return element;
};

TemplateNavigator.prototype.isVisible = function (element) {
    return element.nodeType == 1
        && element.tagName.toUpperCase() != 'SCRIPT'
        && jQuery(element).is(':visible');
};

TemplateNavigator.prototype.addTemplateElement = function (id, element) {
    if (!this.templates[id]) {
        this.templates[id] = element.begin;
    }
};

TemplateNavigator.prototype.getListTemplate = function (element, name) {
    var first = this.getListFirst(element, name);
    var last = this.getListLast(element, name);

    if (!first || !last) {
        return false;
    }

    return {
        begin: jQuery(this.getNextVisibleElement(first)),
        end:   jQuery(this.getPreviousVisibleElement(last)),
        list:  name
    }
};

TemplateNavigator.prototype.getListFirst = function (element, name) {
    var result = null;
    var pattern = new RegExp('\'' + name + '\' list child. +\{\{\{');
    var siblings = element.parentNode.childNodes;

    for (var i = 0; i < siblings.length; i++) {
        var _element = siblings[i];
        if (_element.nodeType == 8 && _element.data.search(pattern) != -1) {
            result = _element;

            break;
        }
    }

    return result;
};

TemplateNavigator.prototype.getListLast = function (element, name) {
    var result = null;
    var pattern = new RegExp('\}\}\} .+\'' + name + '\' list child');
    var siblings = element.parentNode.childNodes;

    for (var i = siblings.length - 1; i >= 0; i--) {
        var _element = siblings[i];
        if (_element.nodeType == 8 && _element.data.search(pattern) != -1) {
            result = _element;

            break;
        }
    }

    return result;
};

TemplateNavigator.prototype.markTemplate = function (element, event) {
    element = jQuery(element).get(0);

    if (element && !element.branch) {
      element.branch = this.getTemplatesBranch(element);
    }

    if (
        typeof element == 'undefined'
        || typeof element.branch == 'undefined'
        || !element.branch.length
        || (this.current && this.current.isSameNode(element))
    ) {
        return false;
    }

    jQuery('.tpl-debug-canvas').remove();
    var templates = element.branch;

    var self = this;

    for (var i = 0; i < templates.length; i++) {
        var t = templates[i];
        var c = document.body.appendChild(document.createElement('div'));
        c.className = i == 0
            ? 'tpl-debug-canvas tpl-debug-current'
            : ('tpl-debug-canvas tpl-debug-' + (t.list ? 'list' : 'tpl') + '-canvas');

        c = jQuery(c);

        c.bind('click',
            function () {
                var treeView = jQuery(self.options.tree).get(0).TreeViewController;
                treeView.selectTemplate(templates[0].id, true);
            }
        );

        c.bind('dblclick',
            function () {
                var treeView = jQuery(self.options.tree).get(0).TreeViewController;
                treeView.selectTemplate(templates[0].id, false);
            }
        );

        var beginPos = t.begin.offset();
        var endPos = t.end.offset();

        var width = t.end.outerWidth();
        var height = t.end.outerHeight();

        c.css(
            {
                top: beginPos.top + 'px',
                left: beginPos.left + 'px',
                width: (endPos.left - beginPos.left + width) + 'px',
                height: (endPos.top - beginPos.top + height) + 'px'
            }
        );

        if (0 == i) {
            this.region = {
                top: beginPos.top,
                left: beginPos.left,
                right: (endPos.left + width),
                bottom: (endPos.top + height)
            };
        }
    }

    this.current = element;

    if (event) {
        this.checkTemplateRegion(event);
    }
};

TemplateNavigator.prototype.unMarkTemplate = function () {
    // Prevent scrolling by storing the page's current scroll offset
    var scrollV = document.body.scrollTop;
    var scrollH = document.body.scrollLeft;

    jQuery('.tpl-debug-canvas').remove();
    this.current = null;
    this.region = null;

    // Restore the scroll offset, should be flicker free
    document.body.scrollTop = scrollV;
    document.body.scrollLeft = scrollH;
};

TemplateNavigator.prototype.markTemplateById = function (id) {
    if (this.templates[id]) {
        this.markTemplate(this.templates[id]);
        var selection = jQuery('.tpl-debug-current');

        if (selection.length) {
            jQuery(document).scrollTop(selection.offset().top);
        }
    } else {
        this.unMarkTemplate();
    }
};

TemplateNavigator.prototype.checkTemplateRegion = function (event) {
    if (
        this.region
    ) {

        var r = this.region;

        if (r.top < event.pageY && r.bottom > event.pageY && r.left < event.pageX && r.right > event.pageX) {

        } else {
            this.unMarkTemplate();
        }
    }
};