/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Products list controller (search blox)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var getUrlByState = function(params) {
  var commonParams = {
    'target': 'search',
    'mode': 'search',
  };

  var _searchUrlParams = core.getCommentedData($('.items-list-products'), 'searchUrlParams') || [];
  var searchUrlParams = [];

  for (var k in _searchUrlParams) {
    searchUrlParams.push(_searchUrlParams[k]);
  }

  var urlParams = core.getUrlParams();

  for (var k in urlParams) {
    if (searchUrlParams.indexOf(k) >= 0) {
      delete urlParams[k];
    }
  }

  params = _.defaults(params, commonParams);
  params = _.defaults(params, urlParams);

  return URLHandler.buildURL(params);
};

var prepareValues = function (rawValues) {
  var values = {};

  _.each(
      rawValues,
      function(el) {
        if (!_.contains(['itemsList', 'returnURL', 'action', 'searchInSubcats'], el.name)) {
          values[el.name] = el.value;
        }
      }
  );

  values = _.reduce(values, function(carry, elem, key) {
    if (elem) {
      carry[key] = elem;
    }

    return carry;
  }, {});

  if (!values) {
    return {};
  }

  return values;
};

var updateHistoryByForm = function (form) {
  if (!form || !form.length) {
    return;
  }

  values = prepareValues(form.serializeArray());

  if (!values) {
    return;
  }

  window.history.replaceState(
      values,
      'Search',
      getUrlByState(values)
  );
};

jQuery(document).ready(function(){
  updateHistoryByForm(jQuery('.search-product-form form'));
});

// Decoration of the products list widget class
core.bind(
  'load',
  function() {
    decorate(
      'ProductsListView',
      'postprocess',
      function(isSuccess, initial)
      {
        arguments.callee.previousMethod.apply(this, arguments);

        if (isSuccess) {
          var o = this;

          // handle "Search" button in the search products form
          if (jQuery(this.base).hasClass('products-search-result')) {
            jQuery('.search-product-form form').unbind('submit').submit(
              function (event)
              {
                if (
                  o.submitForm(
                    this,
                    function (XMLHttpRequest, textStatus, data, isValid) {
                      if (isValid) {

                        var form = jQuery('.search-product-form form');
                        var values = prepareValues(form.serializeArray());
                        updateHistoryByForm(form);

                        o.load(values);
                      } else {
                        o.unshade();
                      }
                    }
                  )
                ) {
                  o.shade();
                }

                return false;
              }
            );
          }

        } // if (isSuccess) {
      } // function(isSuccess, initial)
    ); // 'postprocess' method decoration (EXISTING method)
  }
); // core.bind()
