/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * javascript core
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * OOP core
 */

// OOP extends emulation
function extend(child, parent) {
  var F = function() { };
  F.prototype = parent.prototype;
  child.prototype = new F();
  child.prototype.constructor = child;
  child.superclass = parent.prototype;

  child.prototype.inherit_stack = child.prototype.inherit_stack === undefined ? [] : child.prototype.inherit_stack.slice(0);
  child.prototype.inherit_stack.push(parent);
}

// Decorate / add method
function decorate(c, methodName, method)
{
  c = getClassByName(c);

  var result = false;

  if (c) {
    method.previousMethod = 'undefined' == typeof(c.prototype[methodName]) ? null : c.prototype[methodName];
    c.prototype[methodName] = method;
    result = true;
  }

  return result;
}

// Get class object by name (or object)
function getClassByName(c)
{
  if (c && c.constructor == String) {
    c = eval('(("undefined" != typeof(window.' + c + ') && ' + c + '.constructor == Function) ? ' + c + ' : null)');

  } else if (!c || c.constructor != Function) {
    c = null;
  }

  return c;
}

// Base class
function Base()
{
  this.triggerVent('initialize');
}

// Call parent method by name nad arguments list
Base.prototype.callSupermethod = function(name, args)
{
  var call_stack_head = false;
  this.call_stack = this.call_stack === undefined ? {} : this.call_stack;
  if (!this.call_stack.hasOwnProperty(name) || this.call_stack[name] === undefined) {
    call_stack_head = true;
    this.call_stack[name] = this.inherit_stack.slice(0);
  }

  var superClass = this.call_stack[name].pop();
  var result = (name === 'constructor' ? superClass : superClass.prototype[name]).apply(this, args);
  this.call_stack[name].push(superClass);

  if (call_stack_head) {
    this.call_stack[name] = undefined;
  }

  return result;
}

// Bind event handler
Base.prototype.bind = function(name, handler)
{
  jQuery(this).bind(name, handler);

  return this;
}

// Unbind event handler
Base.prototype.unbind = function(name, handler)
{
  jQuery(this).unbind(name, handler);

  return this;
}

// Trigger event on common mediator object
Base.prototype.triggerVent = function(name, data)
{
  var ns = this.getEventNamespace();
  if (ns) {
    core.trigger(ns + '.' + name, data);
  }

  return jQuery(this).trigger('local.' + name, data);
}

// Get event namespace (prefix)
Base.prototype.getEventNamespace = function()
{
  return null;
}

var reverseArguments = function (func) {
  return function () {
    return func.apply(this, Array.prototype.reverse.call(arguments));
  }
};

// Core definition
window.core = {

  isDebug: false,

  isReady: false,

  isRequesterEnabled: false,

  savedEvents: [],

  messages: jQuery({}),

  resources: [],

  resourcesCacheTimestamp: '',

  htmlResourcesLoadDeferred: new $.Deferred(),

  // Collections of the getters which return the parameters of the widgets to get via widgets collection process
  widgetsParamsGetters: {},

  doShadeWidgetsCollection: true,

  xhrMiddlewares: [],

  getXhrMiddlewares: function () {
    return [
      this.processBackendEvents
    ].concat(this.xhrMiddlewares);
  },

  addXhrMiddleware: function (callback) {
    this.xhrMiddlewares.push(callback);
  },

  registerWidgetsParamsGetter: function (widgetsId, getter)
  {
    if (!this.widgetsParamsGetters[widgetsId]) {
        this.widgetsParamsGetters[widgetsId] = [];
    }
    this.widgetsParamsGetters[widgetsId].push(getter);
  },

  getWidgetsParams: function (widgetsId, paramsToCall)
  {
    var params = [];
    for (var key in this.widgetsParamsGetters[widgetsId]) {
      params = array_merge(params, this.widgetsParamsGetters[widgetsId][key](paramsToCall));
    }
    return params;
  },

  widgetsTriggers: {},

  registerWidgetsTriggers: function (widgetsId, triggersGetter)
  {
    if (!this.widgetsTriggers[widgetsId]) {
      this.widgetsTriggers[widgetsId] = [];
    }
    this.widgetsTriggers[widgetsId].push(triggersGetter);
  },

  getWidgetsTriggers: function (widgetsId)
  {
    var triggers = [];
    for (var key in this.widgetsTriggers[widgetsId]) {
      triggers.push(this.widgetsTriggers[widgetsId][key]());
    }
    return triggers;
  },

  shadowWidgets: {},

  registerShadowWidgets: function (widgetsId, shadowGetter)
  {
    if ("undefined" === typeof(this.shadowWidgets[widgetsId])) {
      this.shadowWidgets[widgetsId] = [];
    }
    this.shadowWidgets[widgetsId].push(shadowGetter);
  },

  getShadowWidgets: function (widgetsId)
  {
    var shadow = [];
    for (var key in this.shadowWidgets[widgetsId]) {
      shadow.push(this.shadowWidgets[widgetsId][key]());
    }
    return array_unique(shadow);
  },

  widgetsTriggersBind: {},

  registerTriggersBind: function (widgetsId, triggersGetter)
  {
    if (!this.widgetsTriggersBind[widgetsId]) {
      this.widgetsTriggersBind[widgetsId] = [];
    }
    this.widgetsTriggersBind[widgetsId].push(triggersGetter);
  },

  callTriggersBind: function (widgetsId)
  {
    for (var key in this.widgetsTriggersBind[widgetsId]) {
      this.widgetsTriggersBind[widgetsId][key]();
    }
  },

  shadeWidgetsCollection: function (base)
  {
    if (this.doShadeWidgetsCollection) {
      jQuery(base).append('<div class="single-progress-mark"><div></div></div>');
    }
    this.doShadeWidgetsCollection = true;
  },

  unshadeWidgetsCollection: function (base)
  {
    jQuery(base + ' .single-progress-mark').remove();
  },

  processUpdateWidgetsCollection: function (widgetsId, widgetsCollection, widgetsCollectionParamsCommon, base)
  {
    // Shadows triggers (unbind and disable the trigger elements)
    var triggers = this.getWidgetsTriggers(widgetsId);
    for (var k in triggers) {
      jQuery(triggers[k]).unbind(k).prop('disabled', 'disabled');
    }
    this.shadeWidgetsCollection('.product-details-info');

    // Shadow widgets collection
    //this.shadowCollection(base, this.getShadowWidgets(widgetsId));

    // Load widgets
    this.loadWidgetsCollection(
      base,
      widgetsCollection,
      array_merge(
        widgetsCollectionParamsCommon,
        this.getWidgetsParams(widgetsId, widgetsCollectionParamsCommon)
      ),
      function ()
      {
        // Unshadow triggers (bind and enable trigger elements)
        for (var k in triggers) {
          jQuery(triggers[k]).removeAttr('disabled');
        }
        core.unshadeWidgetsCollection('.product-details-info');
        core.callTriggersBind(widgetsId);
      }
    );
  },

  // Performs browser redirect on setTimeout
  redirectTo: function(url)
  {
    _.defer(function() {
      window.location.href = url;
    });
  },

  // Trigger common message
  trigger: function(name, params)
  {
    var result = true;

    name = name.toLowerCase();

    if (this.isReady) {

      if (this.isDebug && 'undefined' != typeof(window.console)) {
        if (params) {
          console.log('Fire \'' + name + '\' event with arguments: ' + var_export(params, true));
        } else {
        }
        console.log('Fire \'' + name + '\' event');
      }

      result = this.messages.trigger(name, [params]);

    } else {
      this.savedEvents.push(
        {
          name: name,
          params: params
        }
      );
    }

    return result;
  },

  // Bind on common messages
  bind: function(name, callback)
  {
    if (_.isArray(name)) {
      for(var key in name) {
        if (name.hasOwnProperty(key)) {
          this.messages.bind(name[key].toLowerCase(), callback);
        }
      }
    } else {
      this.messages.bind(name.toLowerCase(), callback);
    }

    return this;
  },

  // Unbind on common messages
  unbind: function(name, callback)
  {
    if (_.isArray(name)) {
      for(var key in name) {
        this.messages.unbind(name[key].toLowerCase(), callback);
      }
    } else {
      this.messages.unbind(name.toLowerCase(), callback);
    }

    return this;
  },

  // Get HTML data from server
  get: function(url, callback, data, options)
  {
    if (_.isObject(url)) {
      url = URLHandler.buildURL(url);
    }

    options = options || {};

    var completeCallback = options.complete || function () {};
    var successCallback = options.success || function () {};

    var headers = {
      'ajaxRefererTarget': core.getTarget(),
    };
    options = jQuery.extend(
      {
        async:       true,
        cache:       false,
        contentType: 'text/html',
        global:      false,
        timeout:     15000,
        type:        'GET',
        url:         url,
        data:        data,
        headers:     headers,
      },
      options,
      {
        complete: function () {
          _.partial(core.onXhrComplete, callback, options, data).apply(this, arguments);
          completeCallback.apply(this, arguments);
        },
        success: function () {
          core.onXhrSuccess.apply(this, arguments);
          successCallback.apply(this, arguments);
        }
      }
    );

    return jQuery.ajax(options);
  },

  // Post form data to server
  post: function(url, callback, data, options)
  {
    if (_.isObject(url)) {
      url = URLHandler.buildURL(url);
    }

    options = options || {};

    var completeCallback = options.complete || function () {};
    var successCallback = options.success || function () {};

    var headers = {
      'ajaxRefererTarget': core.getTarget(),
    };

    options = jQuery.extend(
      {
        async:       true,
        cache:       false,
        contentType: 'application/x-www-form-urlencoded',
        global:      false,
        timeout:     15000,
        type:        'POST',
        url:         url,
        data:        data,
        headers:     headers,
      },
      options,
      {
        complete: function () {
          _.partial(core.onXhrComplete, callback, options, data).apply(this, arguments);
          completeCallback.apply(this, arguments);
        },
        success: function () {
          core.onXhrSuccess.apply(this, arguments);
          successCallback.apply(this, arguments);
        }
      }
    );

    return jQuery.ajax(options);
  },

  onXhrSuccess: function(data, textStatus, XMLHttpRequest) {
      var list = XMLHttpRequest.getAllResponseHeaders().split(/\n/);

      var header = _.find(list, function(headerValue){
          return -1 !== headerValue.search(/update-csrf: (.*)/i);
      });

      var matches = header
          ? header.match(/update-csrf: (.*)/i)
          : null;

      if (matches && matches[1]) {
          var headerData = JSON.parse(matches[1]);

          if (headerData.name === 'xcart_form_id' && headerData.old === xliteConfig.form_id) {
              xliteConfig.form_id = headerData.value;
          }
      }
  },

  onXhrComplete: function(callback, options, data, XMLHttpRequest, textStatus) {
      var callCallback = core.preprocessResponse(XMLHttpRequest, options, callback);
      data = core.processResponse(XMLHttpRequest);
      var notValid = !!XMLHttpRequest.getResponseHeader('not-valid');

      return (callCallback && callback) ? callback(XMLHttpRequest, textStatus, data, !notValid) : true;
  },

  // Response preprocess (run callback or not)
  preprocessResponse: function(xhr, options, callback)
  {
    var result = true;

    var responseStatus = parseInt(xhr.getResponseHeader('ajax-response-status'));

    if (200 == xhr.status && (270 == responseStatus || 279 == responseStatus) && xhr.getResponseHeader('AJAX-Location') && (!options || !options.rpc)) {
      core.get(
        xhr.getResponseHeader('AJAX-Location'),
        callback
      );

      result = false;

    } else if (278 == responseStatus) {

      // Redirect
      var url = xhr.getResponseHeader('AJAX-Location');

      if (url) {
        window.location.replace(url);
      }

      if (!url || this.urlHasHash(url)) {
        window.location.reload(true);
      }

      result = false;
    }

    return result;
  },

  urlHasHash: function(url) {
    return url.indexOf("#") !== -1;
  },

  // Process response from server
  processResponse: function(xhr)
  {
    var response = xhr.responseText;
    this.getXhrMiddlewares().forEach(function(callback) {
      var result = callback(xhr, response);
      if (typeof(result) !== 'undefined') {
        response = result;
      }
    });

    return (4 == xhr.readyState && 200 == xhr.status) ? response : false;
  },

  processBackendEvents: function (xhr) {
    if (4 == xhr.readyState) {
      var list = xhr.getAllResponseHeaders().split(/\n/);

      for (var i = 0; i < list.length; i++) {
        if (-1 !== list[i].search(/^event-([^:]+):(.+)/i)) {

          // Server-side event
          var m = list[i].match(/event-([^:]+):(.+)/i);
          core.trigger(m[1].toLowerCase(), eval('(' + m[2] + ')'));
        }
      }
    }
  },

  showInternalError: function()
  {
    return this.showError(this.t('Javascript core internal error. Page will be refreshed automatically'));
  },

  showServerError: function()
  {
    return this.showError(this.t('Background request to server side is failed. Page will be refreshed automatically'));
  },

  showError: function(message)
  {
    core.trigger(
      'message',
      {'type': 'error', 'message': message}
    );
  },

  translator: null,

  t: function(label, substitute)
  {
    return this.getTranslator().translate(label, substitute);
  },

  loadLanguageHash: function(hash)
  {
    return this.getTranslator().loadLanguageHash(hash);
  },

  getTranslator: function() {
    if (this.translator === null) {
      this.translator = new Translator();
    }

    return this.translator;
  },

  getHtmlResourcesLoadPromise: function () {
    return this.htmlResourcesLoadDeferred.promise();
  },

  loadResource: function(resource, type)
  {
    var deferred = new $.Deferred();
    if (_.isString(resource)) {
      resource = {
        url: resource,
        widget: ''
      }
    }

    type = type || core.getExtension(resource.url);

    if (!resource.url.startsWith("var/theme/custom.css") && core.isLoadedResource(resource.url)) {
      return;
    }

    var url = resource.url;

    if ('' !== core.resourcesCacheTimestamp) {
      url = url
        + (url.indexOf('?') === -1 ? '?' : '&')
        + core.resourcesCacheTimestamp;
    }

    LazyLoad[type](url, function() {
      core.registerResources([url]);
      core.trigger('resources.loaded', {file: url, widget: resource.widget});
      deferred.resolve();
    });

    return deferred.promise();
  },

  isLoadedResource: function(url)
  {
    return _.contains(core.resources, url);
  },

  registerResources: function(list)
  {
    for (var key in list) {
      core.resources.push(list[key]);
    }
  },

  parseResources: function(element, uuid)
  {
    var restoreCallback = core.microhandlers.supressRun();
    uuid = uuid || '';
    element = jQuery(element);
    var resource = element.find('script[data-resource]').first();

    if (resource.length) {
      var data = JSON.parse(resource.html());
      resource.remove();

      if (data.css || data.js) {
        var cssLoaded = core.loadResources(data.css, 'css');
        var jsLoaded = core.loadResources(data.js, 'js');

        jQuery.when(cssLoaded, jsLoaded).done(
          function(){
            restoreCallback();
            core.trigger('resources.ready', {widget: data.widget, uuid: uuid});
          }
        );
      } else {
        restoreCallback();
        core.trigger('resources.empty', {widget: data.widget, uuid: uuid});
      }
    }
  },

  loadResources: function(list, type)
  {
    var deferred = new $.Deferred();
    var promises = [];

    $.when(this.getHtmlResourcesLoadPromise()).then(function(){
      for (var key in list) {
        var resource = list[key];

        promises.push(core.loadResource(resource, type));
      }

      $.when.apply($, promises).then(
        function(){
          deferred.resolve();
        }
      );
    });

    return deferred.promise();
  },

  parsePreloadedLabels: function(element, uuid)
  {
    element = jQuery(element);
    uuid = uuid || '';
    var labelsContainer = element.find('script[data-preloaded-labels]').first();

    if (labelsContainer.length) {
      var data = JSON.parse(labelsContainer.html());
      labelsContainer.remove();

      if (data.labels) {
        core.loadLabels(data.labels);
        core.trigger('preloaded_labels.ready', {widget: data.widget, uuid: uuid});
      } else {
        core.trigger('preloaded_labels.empty', {widget: data.widget, uuid: uuid});
      }
    }
  },

  loadLabels: function(list)
  {
    this.loadLanguageHash(list);
  },

  parseObjectString: function(objectString) {
      if (typeof(JSON5) !== 'undefined') {
        return JSON5.parse(objectString);
      }

      console.error('Cannot parse JS object from string: JSON5 is not loaded');
  },

  getLayoutOptions: function() {
    return window.xliteLayoutOptions || {};
  },

  rest: {

    lastResponse: null,

    request: function(type, name, id, data, callback)
    {
      if (!type || !name) {
        return false;
      }

      this.lastResponse = null;

      var xhr = jQuery.ajax(
        {
          async: false !== callback,
          cache: false,
          complete: function(xhr, status) {
            return this.callback(xhr, status, callback);
          },
          context: this,
          data: data,
          timeout: 30000,
          type: ('get' == type ? 'GET' : 'POST'),
          url: URLHandler.buildURL(
            {
              target: 'rest',
              action: type,
              name:   name,
              id:     id
            }
          )
        }
      );

      if (false === callback) {
        xhr = (this.lastResponse && this.lastResponse.status == 'success') ? this.lastResponse.data : null;
      }

      return xhr;
    },

    get: function(name, id, callback) {
      return this.request('get', name, id, null, callback);
    },

    post: function(name, id, data, callback) {
      return this.request('post', name, id, data, callback);
    },

    put: function(name, data, callback) {
      return this.request('put', name, null, data, callback);
    },

    'delete': function(name, id, callback) {
      return this.request('delete', name, id, null, callback);
    },

    callback: function(xhr, status, callback)
    {
      try {
        var data = jQuery.parseJSON(xhr.responseText);

      } catch(e) {
        var data = null;
      }

      if (false === callback) {
        core.rest.lastResponse = data;

      } else if (callback) {
        callback(xhr, status, data);
      }
    }
  },


  clearHash: function (paramName) {
    var scrollV, scrollH, loc = window.location;
    var newHash = '';

    if (paramName !== undefined) {
      var pattern = '&?(' + paramName + '(\\[\\S*\\])*=[^&]*&?)';
      var re = new RegExp(pattern, "gi");

      newHash = loc.hash.replace(re, '').slice(1);
    };

    // Prevent scrolling by storing the page's current scroll offset
    scrollV = document.body.scrollTop;
    scrollH = document.body.scrollLeft;

    if (newHash !== loc.hash) {
      loc.hash = newHash
    }

    // Restore the scroll offset, should be flicker free
    document.body.scrollTop = scrollV;
    document.body.scrollLeft = scrollH;
  },

  autoload: function(className, element)
  {
    if ('function' == typeof(className)) {
      var m = className.toString().match(/function ([^\(]+)/);
      window[m[1]] = className;
      className = m[1];
    }

    var classObject = window[className];

    jQuery(document).ready(
      function() {
        if ('undefined' != typeof(classObject)) {
          if ('undefined' != typeof(element)) {
            jQuery(element).each(function(index, el) {
             jQuery(el).data('controller', new (classObject)(jQuery(el)));
           });
          } else if ('function' == typeof(classObject.autoload)) {
            classObject.autoload();

          } else {
            new (classObject)();
          }
        }
      }
    );
  },

  // Return value of variable that is given in class attribute: e.g. class="superclass productid-100001 test"
  getValueFromClass: function(obj, prefix)
  {
    var m = jQuery(obj)
      .attr('class')
      .match(new RegExp(prefix + '-([^ ]+)( |$)'));

    return m ? m[1] : null;
  },

  // Return value of variable that is given in comment block: e.g. <!-- 'productid': '100001', 'var': 'value', -->"
  getCommentedData: function(obj, name)
  {
    if (jQuery(obj).get(0)) {
      var children = jQuery(obj).get(0).childNodes;
    } else {
      return name ? null : [];
    }
    var m = false;

    for (var i = 0; i < children.length && !m; i++) {
      if ('SCRIPT' == children[i].nodeName && 'text/x-cart-data' == children[i].type) {
        m = children[i].innerHTML;
        m = m.replace(/^\n\r/, '').replace(/\r\n$/, '');
        try {
          m = eval('(' + m + ')');
        } catch(e) {
          m = false;
        }
      }
    }

    if (m && name) {
      m = 'undefined' == typeof(m[name]) ? null : m[name];
    }

    return m ? m : null;
  },

  // Toggle link text and toggle obj visibility
  toggleText : function (link, text, obj)
  {
    if (undefined === link.prevValue) {
      link.prevValue = jQuery(link).html();
    }
    jQuery(link).html(jQuery(link).html() === text ? link.prevValue : text);
    jQuery(obj).toggle();
  },

  setRelArray: function ($obj, relArray)
  {
    $obj.attr('data-rel', _.map(_.pairs(relArray), function (value) {return value.join(": ");}).join(", "));
  },

  getRelArray: function ($obj)
  {
    var rel = $obj.attr('data-rel');

    return _.object(_.map(rel.split(','), function (value, key, list) {
      return _.map(_.trim(value).split(':'), function (value) {return _.trim(value);});
    }));
  },

  // Decorate class after page loading
  decorate: function(className, methodName, func)
  {
    core.bind(
      'load',
      function() {
        decorate(className, methodName, func);
      }
    );
  },

  // Decorate some class after page loading
  decorates: function(list, func)
  {
    core.bind(
      'load',
      function() {
        for (var i = 0; i < list.length; i++) {
          decorate(list[i][0], list[i][1], func);
        }
      }
    );
  },

  stringToNumber: function(number, dDelim, tDelim)
  {
    if ("" !== tDelim) {
      number = number.replace(new RegExp("\\" + tDelim, 'g'), '');
    }

    var a = number.split(dDelim);

    return parseFloat(a[0] + '.' + a[1]);
  },

  numberToString: function(number, dDelim, tDelim, precision)
  {
    dDelim = dDelim || '.';
    tDelim = tDelim || '';
    precision = precision || 0;

    precision = Math.max(parseInt(precision), 0);

    number = this.round(number, precision).toString();
    /*
      Author: Robert Hashemian
      http://www.hashemian.com/

      You can use this code in any manner so long as the author's name,
      Web address and this disclaimer is kept intact.
     ********************************************************/

    var a = number.split('.');
    var x = a[0]; // decimal
    var y = a[1]; // fraction
    var z = '';

    if (typeof(x) != 'undefined') {
      // reverse the digits. regexp works from left to right.
      for (var i = x.length - 1; i >= 0; i--) {
        z += x.charAt(i);
      }

      // add separators. but undo the trailing one, if there
      z = z.replace(/(\d{3})/g, "$1" + tDelim);

      if (z.slice(-tDelim.length) == tDelim){
        z = z.slice(0, -tDelim.length);
      }

      x = '';

      // reverse again to get back the number
      for ( i = z.length - 1; i >= 0; i--) {
        x += z.charAt(i);
      }

      // add the fraction back in, if it was there
      if (typeof(y) != 'undefined' && y.length > 0) {
        x += dDelim + y + this.repeat('0', precision - y.length);

      } else if (precision > 0) {
        x += dDelim + this.repeat('0', precision);
      }
    }

    return x;
  },

  getExtension: function(path)
  {
    return path.slice((path.lastIndexOf(".") - 1 >>> 0) + 2);
  },

  repeat: function(s, n)
  {
    var a = [];
    while (a.length < n){
        a.push(s);
    }

    return a.join('');
  },

  round: function(number, precision)
  {
    precision = precision || 0;
    precision = Math.max(parseInt(precision), 0);

    var pow = Math.pow(10, precision);

    return Math.round(number * pow) / pow;
  },

  loadWidgetsCollection: function (base, collectionName, params, restoreCallback)
  {
    this.get(
      URLHandler.buildURL(array_merge({'target': 'widgets_collection', 'action': 'get', 'widget': collectionName}, params)),
      function (XMLHttpRequest, textStatus, data) {
        try {
          data = JSON.parse(data);
          for (var key in data) {
            jQuery('.' + data[key].view, jQuery(base)).replaceWith(data[key].content);
//            core.unshadowWidget(base, '.' + data[key].view);
          }
          restoreCallback();

        } catch (error) {
          console.log(error.message);
        }
      }
    );
  },

  shadowCollection: function (base, collection) {
    for (key in collection) {
      jQuery(collection[key], jQuery(base)).after('<div class="single-progress-mark"></div>');
    }
  },

  unshadowWidget: function (base, widget) {
    jQuery('.single-progress-mark', jQuery(widget, jQuery(base)).parent()).remove();
  },

  getURLParam: function (name) {
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec(window.location.href);
    if( results == null )
        return "";
    else
        return results[1];
  },

  getUrlParams: function (url) {
    if (_.isUndefined(url)) {
      url = window.location.search;
    }

    if (url.indexOf('?') >= 0) {
      url = url.substr(url.indexOf('?') + 1);
    }

    var searchParams = new URLSearchParams(url)

    var result = []
    for (const [key, value] of searchParams) {
      result[key] = searchParams.get(key)
    }
    return result
  },

  getTarget: function () {
    return xliteConfig.target;
  },

  getFormIdString: function () {
    return xliteConfig.form_id_name + '=' + xliteConfig.form_id;
  }

};

// HTTP requester detection
try {

  var xhr = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
  core.isRequesterEnabled = xhr ? true : false;

} catch(e) { }

// Common onready event handler
jQuery(document).ready(
  function() {
    core.isReady = true;
    core.trigger('load');
    for (var i = 0; i < core.savedEvents.length; i++) {
        core.trigger(core.savedEvents[i].name, core.savedEvents[i].params);
    }
    core.savedEvents = [];
  }
);

core.isDeveloperMode = xliteConfig.developer_mode;
core.isCloud = xliteConfig.cloud;

/**
 * Common functions
 */

// Check - specified object is HTML element or not
function isElement(obj, type)
{
  return obj && typeof(obj.tagName) != 'undefined' && obj.tagName.toLowerCase() == type;
}

jQuery.extend({
  getQueryParameters : function(str) {
    return (str || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];
  }
});

core.bind(
  'load',
  function () {
    jQuery('input[type=checkbox]').each(
      function () {
        var checkbox = this;

        jQuery(checkbox).bind(
          'click',
          function () {
            return !jQuery(checkbox).prop('readonly');
          }
        );
      }
    );

    jQuery('.model-form-buttons .button:first-child button').addClass('btn-warning');
  }
);

/* Microhandlers */

core.microhandlers = {};

core.microhandlers.list = {};

core.microhandlers.initialRunned = false;
core.microhandlers.supressRunOnAdd = false;

core.microhandlers.supressRun = function() {
  var previous = this.supressRunOnAdd;
  this.supressRunOnAdd = true;

  return _.bind(
    function() {
      this.supressRunOnAdd = false;
    },
    this
  );
};

core.microhandlers.add = function(name, pattern, handler)
{
  this.list[name] = {
    'pattern': pattern,
    'handler': handler
  };

  if (this.initialRunned && !this.supressRunOnAdd) {
    this.run(name);
  }
};

core.microhandlers.run = function(name, base)
{
  base = base || document;

  if ('undefined' != typeof(this.list[name]) && this.list[name]) {
    if (_.isString(this.list[name].pattern)) {
      jQuery(this.list[name].pattern, base)
        .filter(_.bind(this.filterByMark(name), this))
        .each(this.list[name].handler)
        .each(_.bind(this.assignMark(name), this));

    } else if (_.isFunction(this.list[name].pattern)) {
      this.list[name].pattern()
        .filter(_.bind(this.filterByMark(name), this))
        .each(this.list[name].handler)
        .each(_.bind(this.assignMark(name), this));
    }
  }
};

core.microhandlers.runInitial = function()
{
  this.initialRunned = true;
  this.runAll(document);
};

core.microhandlers.runAll = function(base)
{
  _.each(
    _.keys(this.list),
    function(name) {
      this.run(name, base);
    },
    this
  );
};

core.microhandlers.filterByMark = function(name)
{
  return function(idx, elm) {
    return !jQuery(elm).data('microhandler-' + name);
  };
};

core.microhandlers.assignMark = function(name)
{
  return function(idx, elm) {
    jQuery(elm).data('microhandler-' + name, true);
  };
};

core.microhandlers.add(
  'BootstrapTooltip',
  '[data-toggle="tooltip"]',
  function(event) {
    jQuery(this).tooltip();
  }
);

core.microhandlers.add(
  'BootstrapPopover',
  '[data-toggle="popover"]',
  function(event) {
    if (!_.isUndefined(jQuery(this).startTooltip)) {
      jQuery(this).startTooltip();
    }
  }
);

core.bind(
  'load',
  function() {
    core.microhandlers.runInitial();
  }
);

core.bind(
  'loader.loaded',
  function(event, widget) {
    core.microhandlers.runAll(widget.base);
  }
);


core.bind(
  'vue-form.ready',
  function(event, element) {
    core.microhandlers.runAll(element);
  }
);

if (window.CoreAMD !== undefined) {
  define('js/core', function () {
    return core;
  });
} else {
  document.addEventListener('amd-ready', function (event) {
    define('js/core', function () {
      return core;
    });
  });
}
