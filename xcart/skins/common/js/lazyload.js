/*jslint browser: true, eqeqeq: true, bitwise: true, newcap: true, immed: true, regexp: false */

/**
LazyLoad makes it easy and painless to lazily load one or more external
JavaScript or CSS files on demand either during or after the rendering of a web
page.

Supported browsers include Firefox 3.6+, IE9+, Safari 5.1.4+ (untested) (including Mobile
Safari), Google Chrome 10+, and Opera 12+. Other browsers may or may not work and
are not officially supported, including the browsers supported by the original LazyLoad:
Firefox 2+, IE6+, Safari 3+, Google Chrome 0.8+, and Opera 9+ (not yet tested).

Visit https://github.com/lewisje/lazyload/ for more info.

Copyright (c) 2011 Ryan Grove <ryan@wonko.com>
All rights reserved.
Changes made by Cezary Daniel Nowak, 2014, and James Edward Lewis II, 2015.

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the 'Software'), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

@module lazyload
@class LazyLoad
@static
*/

var LazyLoad = (function (doc) {
  'use strict';
  // -- Private Variables ------------------------------------------------------

  // Reference to the <head> element (populated lazily).
  var head,

  // Requests currently in progress, if any.
  pending = {},

  // Number of times we've polled to check whether a pending stylesheet has
  // finished loading. If this gets too high, we're probably stalled.
  pollCount = 0,

  // Cached requests.
  cache = {},

  // Queued requests.
  queue = {css: [], js: []},
  
  // Whether Function#bind exists.
  canBind = typeof Function.prototype.bind === 'function',

  // Reference to the browser's list of stylesheets.
  styleSheets = doc.styleSheets,
  finishCSS = canBind ? finish.bind(doc, 'css') : function finishCSS() {finish('css');},
  ua = navigator.userAgent,
  // User agent and feature test information.
  env = {
    // True if this browser supports disabling async mode on dynamically
    // created script nodes. See
    // http://wiki.whatwg.org/wiki/Dynamic_Script_Execution_Order
    async: createNode('script').async === true
  };

  if (/AppleWebKit\//.test(ua)) env.webkit = true;
  else if (/MSIE|Trident/.test(ua)) env.ie = true;
  else if (/Opera/.test(ua)) env.opera = true;
  else if (/Gecko\//.test(ua)) env.gecko = true;
  else env.unknown = true;
  // -- Private Methods --------------------------------------------------------

  /**
  Creates and returns an HTML element with the specified name and attributes.

  @method createNode
  @param {String} name element name
  @param {Object} attrs name/value mapping of element attributes
  @return {HTMLElement}
  @private
  */
  function createNode(name, attrs) {
    var attr,
        node = doc.createElement(name);

    if(attrs) {
      for (attr in attrs) {
        if (attrs.hasOwnProperty(attr)) {
          node.setAttribute(attr, attrs[attr]);
        }
      }
    }

    return node;
  }

  /**
  Called when the current pending resource of the specified type has finished
  loading. Executes the associated callback (if any) and loads the next
  resource in the queue.

  @method finish
  @param {String} type resource type ('css' or 'js')
  @private
  */
  function finish(type) {
    var p = pending[type], callback, urls;

    if (p) {
      callback = p.callback;
      urls = p.urls;

      cache[urls.shift()] = true;
      pollCount = 0;

      // If this is the last of the pending URLs, execute the callback and
      // start the next request in the queue (if any).
      if (!urls.length) {
        if (typeof callback === 'function') callback.call(p.context, p.obj);
        pending[type] = 0;
        if (queue[type].length) load(type);
      }
    }
  }

  /**
  Loads the specified resources, or the next resource of the specified type
  in the queue if no resources are specified. If a resource of the specified
  type is already being loaded, the new request will be queued until the
  first request has been finished.

  When an array of resource URLs is specified, those URLs will be loaded in
  parallel if it is possible to do so while preserving execution order. All
  browsers support parallel loading of CSS, but only Firefox and Opera
  support parallel loading of scripts. In other browsers, scripts will be
  queued and loaded one at a time to ensure correct execution order.

  @method load
  @param {String} type resource type ('css' or 'js')
  @param {String|Array} urls (optional) URL or array of URLs to load
  @param {Function} callback (optional) callback function to execute when the
    resource is loaded
  @param {Object} obj (optional) object to pass to the callback function
  @param {Object} context (optional) if provided, the callback function will
    be executed in this object's context
  @private
  */
  function load(type, urls, callback, obj, context) {
    var _finish = canBind ? finish.bind(doc, type) : function _finish() {finish(type);},
        isCSS = type === 'css', nodes = [], i, len, node, p, pendingUrls, url;

    if (urls) {
      // If urls is a string, wrap it in an array. Otherwise assume it's an
      // array and create a copy of it so modifications won't be made to the
      // original.
      urls = typeof urls === 'string' ? [urls] : urls.slice();

      // Create a request object for each URL. If multiple URLs are specified,
      // the callback will only be executed after all URLs have been loaded.
      //
      // Sadly, Firefox and Opera are the only browsers capable of loading
      // scripts in parallel while preserving execution order. In all other
      // browsers, scripts must be loaded sequentially.
      //
      // All browsers respect CSS specificity based on the order of the link
      // elements in the DOM, regardless of the order in which the stylesheets
      // are actually downloaded.
      if (isCSS || env.async || env.gecko || env.opera) {
        // Load in parallel.
        queue[type].push({
          urls : urls,
          callback: callback,
          obj : obj,
          context : context
        });
      } else {
        // Load sequentially.
        for (i = 0, len = urls.length; i < len; ++i) {
          queue[type].push({
            urls : [urls[i]],
            callback: i === len - 1 ? callback : 0, // callback is only added to the last URL
            obj : obj,
            context : context
          });
        }
      }
    }

    // If a previous load request of this type is currently in progress, we'll
    // wait our turn. Otherwise, grab the next item in the queue.
    if (pending[type] || !(p = pending[type] = queue[type].shift())) return;
    head = head || doc.head || doc.getElementsByTagName('head')[0];
    pendingUrls = p.urls.slice();

    var nodeReady = function nodeReady() {
      if (/^(?:loaded|complete)$/i.test(node.readyState)) {
        node.onreadystatechange = null;
        _finish();
      }
    };
    for (i = 0, len = pendingUrls.length; i < len; ++i) {
      url = pendingUrls[i];
      if(cache[url] != null && !url.startsWith("var/theme/custom.css")) {
        // _finish here can cause unexpected behavior when cache[url] === false but
        // I won't figure out solution, since first issue on github :-)
        _finish();
        continue; //prevent scripts/css from being loaded multiple times
      } else {
        cache[url] = false;
      }
      if (isCSS) {
        node = env.gecko ? createNode('style') : createNode('link', {
          href: url,
          rel : 'stylesheet'
        });
      } else {
        node = createNode('script', {src: url});
        node.async = false;
      }

      node.className = 'lazyload';
      node.setAttribute('charset', 'utf-8');

      if (env.ie && !isCSS && 'onreadystatechange' in node && !('draggable' in node)) {
        node.onreadystatechange = nodeReady;
      } else if (isCSS && (env.gecko || env.webkit)) {
        // Gecko and WebKit don't support the onload event on link nodes.
        if (env.webkit) {
          // In WebKit, we can poll for changes to document.styleSheets to
          // figure out when stylesheets have loaded.
          p.urls[i] = node.href; // resolve relative URLs (or polling won't work)
          pollWebKit();
        } else {
          // In Gecko, we can import the requested URL into a <style> node and
          // poll for the existence of node.sheet.cssRules. Props to Zach
          // Leatherman for calling my attention to this technique.
          node.innerHTML = '@import "' + url + '";';
          pollGecko(node);
        }
      } else {
        node.onload = node.onerror = _finish;
      }
      nodes.push(node);
    }
    for (i = 0, len = nodes.length; i < len; ++i) {
      head.appendChild(nodes[i]);
    }
  }

  /**
  Begins polling to determine when the specified stylesheet has finished loading
  in Gecko. Polling stops when all pending stylesheets have loaded or after 10
  seconds (to prevent stalls).

  Thanks to Zach Leatherman for calling my attention to the @import-based
  cross-domain technique used here, and to Oleg Slobodskoi for an earlier
  same-domain implementation. See Zach's blog for more details:
  http://www.zachleat.com/web/2010/07/29/load-css-dynamically/

  @method pollGecko
  @param {HTMLElement} node Style node to poll.
  @private
  */
  function pollGecko(node) {
    var hasRules = 'sheet' in node && node.sheet && 'cssRules' in node.sheet;

    if (!hasRules) {
      // The stylesheet is still loading.
      ++pollCount;
      if (pollCount < 200) {
        setTimeout(canBind ? pollGecko.bind(doc, node) : function pollBind() {pollGecko(node);}, 50);
      } else {
        // We've been polling for 10 seconds and nothing's happened. Stop
        // polling and finish the pending requests to avoid blocking further
        // requests.
        if (hasRules) finishCSS();
      }
      return;
    }
    // If we get here, the stylesheet has loaded.
    finishCSS();
  }

  /**
  Begins polling to determine when pending stylesheets have finished loading
  in WebKit. Polling stops when all pending stylesheets have loaded or after 10
  seconds (to prevent stalls).

  @method pollWebKit
  @private
  */
  function pollWebKit() {
    var css = pending.css, i;

    if (css) {
      i = styleSheets.length;
      // Look for a stylesheet matching the pending URL.
      while (--i >= 0) {
        if (styleSheets[i].href === css.urls[0]) {
          finishCSS();
          break;
        }
      }

      ++pollCount;
      if (css) {
        if (pollCount < 200) {
          setTimeout(pollWebKit, 50);
        } else {
          // We've been polling for 10 seconds and nothing's happened, which may
          // indicate that the stylesheet has been removed from the document
          // before it had a chance to load. Stop polling and finish the pending
          // request to prevent blocking further requests.
          finishCSS();
        }
      }
    }
  }

  return {
    /**
    Requests the specified CSS URL or URLs and executes the specified
    callback (if any) when they have finished loading. If an array of URLs is
    specified, the stylesheets will be loaded in parallel and the callback
    will be executed after all stylesheets have finished loading.

    @method css
    @param {String|Array} urls CSS URL or array of CSS URLs to load
    @param {Function} callback (optional) callback function to execute when
      the specified stylesheets are loaded
    @static
    */
    css: canBind ? load.bind(load, 'css') : function css(urls, callback, obj, context) {
      load('css', urls, callback, obj, context);
    },

    /**
    Requests the specified JavaScript URL or URLs and executes the specified
    callback (if any) when they have finished loading. If an array of URLs is
    specified and the browser supports it, the scripts will be loaded in
    parallel and the callback will be executed after all scripts have
    finished loading.

    Currently, only Firefox and Opera support parallel loading of scripts while
    preserving execution order. In other browsers, scripts will be
    queued and loaded one at a time to ensure correct execution order.

    @method js
    @param {String|Array} urls JS URL or array of JS URLs to load
    @param {Function} callback (optional) callback function to execute when
      the specified scripts are loaded
    @static
    */
    js: canBind ? load.bind(load, 'js') : function js(urls, callback, obj, context) {
      load('js', urls, callback, obj, context);
    }
  };
})(this.document);
