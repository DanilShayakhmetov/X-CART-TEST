(function (_, $) {
    function deepClone(object) {
        // Preserve keys with undefined values by changing the values to null
        return JSON.parse(JSON.stringify(object, function(k, v) {
            return v === undefined ? null : v;
        }));
    }

    var selector = '#cloud-filters',
        mobileLinkSelector = '#cloud-filters-mobile-link';

    var initialData = core.getCommentedData($(selector));

    var predefinedColors = {'aliceblue': '#f0f8ff', 'antiquewhite': '#faebd7', 'aqua': '#00ffff', 'aquamarine': '#70db93', 'azure': '#f0ffff', 'beige': '#f5f5dc', 'bisque': '#ffe4c4', 'black': '#000000', 'blanchedalmond': '#ffebcd', 'blue': '#3232cd', 'blueviolet': '#8a2be2', 'brass': '#b5a642', 'brightgold': '#d9d919', 'bronze': '#8c7853', 'bronzeii': '#a67d3d', 'brown': '#a52a2a', 'burlywood': '#deb887', 'cadetblue': '#5f9ea0', 'chartreuse': '#7fff00', 'chocolate': '#5c3317', 'coolcopper': '#d98719', 'copper': '#b87333', 'coral': '#ff7f00', 'cornflowerblue': '#42426f', 'cornsilk': '#fff8dc', 'crimson': '#dc143c', 'cyan': '#00ffff', 'darkblue': '#00008b', 'darkbrown': '#5c4033', 'darkcyan': '#008b8b', 'darkgoldenrod': '#b8860b', 'darkgreen': '#2f4f2f', 'darkgreencopper': '#4a766e', 'darkgrey': '#a9a9a9', 'darkkhaki': '#bdb76b', 'darkmagenta': '#8b008b', 'darkolivegreen': '#4f4f2f', 'darkorange': '#ff8c00', 'darkorchid': '#9932cd', 'darkpurple': '#871f78', 'darkred': '#8b0000', 'darksalmon': '#e9967a', 'darkseagreen': '#8fbc8f', 'darkslateblue': '#6b238e', 'darkslategrey': '#2f4f4f', 'darktan': '#97694f', 'darkturquoise': '#7093db', 'darkviolet': '#9400d3', 'darkwood': '#855e42', 'deeppink': '#ff1493', 'deepskyblue': '#00bfff', 'dimgrey': '#545454', 'dodgerblue': '#1e90ff', 'dustyrose': '#856363', 'fadedbrown': '#f5ccb0', 'feldspar': '#d19275', 'firebrick': '#8e2323', 'floralwhite': '#fffaf0', 'forestgreen': '#238e23', 'fuchsia': '#ff00ff', 'gainsboro': '#dcdcdc', 'ghostwhite': '#f8f8ff', 'gold': '#ffd700', 'goldenrod': '#dbdb70', 'green': '#238e23', 'greencopper': '#527f76', 'greenyellow': '#93db70', 'grey': '#d8d8d8', 'gray': '#d8d8d8', 'honeydew': '#f0fff0', 'hotpink': '#ff69b4', 'huntergreen': '#215e21', 'indianred': '#4e2f2f', 'indigo': '#4b0082', 'ivory': '#fffff0', 'khaki': '#9f9f5f', 'lavender': '#e6e6fa', 'lavenderblush': '#fff0f5', 'lawngreen': '#7cfc00', 'lemonchiffon': '#fffacd', 'lightblue': '#c0d9d9', 'lightcoral': '#f08080', 'lightcyan': '#e0ffff', 'lightgoldenrodyellow': '#fafad2', 'lightgreen': '#90ee90', 'lightgrey': '#a8a8a8', 'lightpink': '#ffb6c1', 'lightsalmon': '#ffa07a', 'lightseagreen': '#20b2aa', 'lightskyblue': '#87cefa', 'lightslateblue': '#8470ff', 'lightslategrey': '#778899', 'lightsteelblue': '#8f8fbd', 'lightwood': '#e9c2a6', 'lightyellow': '#ffffe0', 'lime': '#00ff00', 'limegreen': '#32cd32', 'linen': '#faf0e6', 'magenta': '#ff00ff', 'mandarianorange': '#e47833', 'maroon': '#800000', 'mediumaquamarine': '#32cd99', 'mediumblue': '#3232cd', 'mediumforestgreen': '#6b8e23', 'mediumgoldenrod': '#eaeaae', 'mediumorchid': '#9370db', 'mediumpurple': '#9370db', 'mediumseagreen': '#426f42', 'mediumslateblue': '#7f00ff', 'mediumspringgreen': '#7fff00', 'mediumturquoise': '#70dbdb', 'mediumvioletred': '#db7093', 'mediumwood': '#a68064', 'midnightblue': '#2f2f4f', 'mintcream': '#f5fffa', 'mistyrose': '#ffe4e1', 'moccasin': '#ffe4b5', 'navajowhite': '#ffdead', 'navy': '#000080', 'navyblue': '#23238e', 'neonblue': '#4d4dff', 'neonpink': '#ff6ec7', 'newmidnightblue': '#00009c', 'newtan': '#ebc79e', 'oldgold': '#cfb53b', 'oldlace': '#fdf5e6', 'olive': '#808000', 'olivedrab': '#6b8e23', 'orange': '#ff7f00', 'orangered': '#ff2400', 'orchid': '#db70db', 'palegoldenrod': '#eee8aa', 'palegreen': '#8fbc8f', 'paleturquoise': '#afeeee', 'palevioletred': '#d87093', 'papayawhip': '#ffefd5', 'peachpuff': '#ffdab9', 'peru': '#cd853f', 'pink': '#bc8f8f', 'plum': '#eaadea', 'powderblue': '#b0e0e6', 'purple': '#800080', 'quartz': '#d9d9f3', 'red': '#ff0000', 'richblue': '#5959ab', 'rosybrown': '#bc8f8f', 'royalblue': '#4169e1', 'rosegold': '#f3cec8', 'saddlebrown': '#8b4513', 'salmon': '#fa8072', 'sandybrown': '#f4a460', 'scarlet': '#8c1717', 'seagreen': '#238e68', 'seashell': '#fff5ee', 'semi-sweetchocolate': '#6b4226', 'sienna': '#8e6b23', 'silver': '#e6e8fa', 'skyblue': '#87ceeb', 'slateblue': '#007fff', 'slategrey': '#708090', 'snow': '#fffafa', 'spacegray': '#65737e', 'spicypink': '#ff1cae', 'springgreen': '#00ff7f', 'steelblue': '#236b8e', 'summersky': '#38b0de', 'tan': '#db9370', 'teal': '#008080', 'thistle': '#d8bfd8', 'tomato': '#ff6347', 'turquoise': '#adeaea', 'verylightgrey': '#cdcdcd', 'violet': '#4f2f4f', 'violetred': '#cc3299', 'wheat': '#d8d8bf', 'white': '#ffffff', 'whitesmoke': '#f5f5f5', 'yellow': '#ffff00', 'yellowgreen': '#99cc32'};

    var getNormalizedColorName = function (name) {
        return name.toLowerCase().replace(/[\s]/g, '');
    };

    var colors = _.reduce(
        _.extend({}, predefinedColors, initialData.colorFilterValues),
        function (result, value, key) {
            result[getNormalizedColorName(key)] = value;

            return result;
        },
        {}
    );

    var getColorByName = function (name) {
        var normalized = getNormalizedColorName(name);

        return colors.hasOwnProperty(normalized) ? colors[normalized] : null;
    };

    var sizes = ['XXXS', 'XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '2XL', '3XL', '4XL', '5XL'];

    var FILTER_PARAM_PREFIX = 'filter_';

    var paramsToQueryString = function (params) {
        return _.map(params, function (p) {
            return encodeURIComponent(p[0]) + '=' + encodeURIComponent(p[1]);
        }).join('&');
    };

    var queryStringToParams = function () {
        return _.chain(window.location.search.slice(1).split('&'))
            .map(function (item) {
                if (item) return _.map(item.split('='), decodeURIComponent);
            })
            .compact()
            .value();
    };

    var filtersToParamArray = function (filters) {
        var ARRAY_VALUES_SEP = '__';

        return _.flatten(_.map(filters, function (_values, key) {
            var values = _.filter(Array.isArray(_values) ? _values : [_values], function (value) {
                return value !== null;
            });

            return _.map(values, function (value) {
                return [
                    FILTER_PARAM_PREFIX + key,
                    Array.isArray(value) ? value.join(ARRAY_VALUES_SEP) : value
                ];
            })
        }), true);
    };

    var hashAdd = window.hash.add;

    window.hash.add = function (params) {
        return hashAdd.call(this, _.omit(params, 'cloudFilters'));
    };

    if (ALoadable.prototype.doReplaceState) {
        ALoadable.prototype.doReplaceState = function (params) {
            var o = this, origParams = {};
            var filtersStr = paramsToQueryString(filtersToParamArray(params.cloudFilters || {}));

            window.location.search.substring(1).split('&').forEach(function (v) {
                var pair = v.split('=');

                if (
                    !_.isUndefined(pair[0])
                    && !_.isUndefined(pair[1])
                    && pair[0] !== o.replaceStatePrefix
                    && pair[0].indexOf(o.replaceStatePrefix + '[') !== 0
                    && pair[0].indexOf('filter_') !== 0
                ) {
                    origParams[pair[0]] = pair[1];
                }
            });

            var url = window.location.pathname + '?';
            var queryString = '';
            params = array_merge(origParams, _.omit(params, 'cloudFilters') || {});

            $.each(params, function (key, value) {
                queryString += '&' + key + '=' + value;
            });

            queryString += filtersStr ? '&' + filtersStr : '';

            url += queryString.substring(1);

            window.history.replaceState(params, 'Filter', url);
        };
    }

    // Modify core.getUrlByState to support multiple parameters with same name
    if (window.getUrlByState) {
        var getUrlByState = window.getUrlByState;

        window.getUrlByState = function (params) {
            var _getUrlParams = core.getUrlParams;

            core.getUrlParams = function () {
                var params = queryStringToParams();

                return _.reduce(params, function (paramsDict, kv) {
                    if (!paramsDict.hasOwnProperty(kv[0])) {
                        paramsDict[kv[0]] = kv[1];
                    } else {
                        if (!_.isArray(paramsDict[kv[0]])) {
                            paramsDict[kv[0]] = [paramsDict[kv[0]]];
                        }

                        paramsDict[kv[0]].push(kv[1]);
                    }

                    return paramsDict;
                }, {});
            };

            var _implodeParams = URLHandler.implodeParams;

            URLHandler.implodeParams = function (params) {
               var paramTuples = [].concat.apply([], _.map(params, function (v, k) {
                   if (_.isArray(v)) {
                       return _.map(v, function (vv) {
                           return [k, vv];
                       })
                   }

                   return [[k, v]];
               }));

               return paramsToQueryString(paramTuples);
            };

            var url = getUrlByState.call(this, params);

            // Restore original URLHandler.implodeParams & core.getUrlParams
            URLHandler.implodeParams = _implodeParams;
            core.getUrlParams = _getUrlParams;

            return url;
        }
    }

    var productList = (function () {
        var load, cloudFilters, searchListeners = [], afterLoadListeners = [];

        function decorateConcreteWidgetClasses(widgetClasses, c, methodName, method) {
            var f = function () {
                method.previousMethod = arguments.callee.previousMethod;

                return widgetClasses.indexOf(this.widgetClass) !== -1
                    ? method.apply(this, arguments)
                    : arguments.callee.previousMethod.apply(this, arguments);
            };

            decorate(c, methodName, f);
        }

        var decorateClasses = [
            'XLite\\View\\ItemsList\\Product\\Customer\\Search',
            'XLite\\View\\ItemsList\\Product\\Customer\\Category\\Main',
            'XLite\\Module\\XC\\ProductFilter\\View\\ItemsList\\Product\\Customer\\Category\\CategoryFilter',
            'XLite\\Module\\CDev\\Sale\\View\\SalePage',
            'XLite\\Module\\CDev\\Bestsellers\\View\\BestsellersPage',
            'XLite\\Module\\CDev\\ProductAdvisor\\View\\NewArrivalsPage',
            'XLite\\Module\\CDev\\ProductAdvisor\\View\\ComingSoonPage',
            'XLite\\Module\\XC\\MultiVendor\\View\\ItemsList\\Product\\Customer\\Vendor',
            'XLite\\Module\\QSL\\ShopByBrand\\View\\ItemsList\\Product\\Customer\\Brand'
        ];

        core.bind(
            'load',
            function () {
                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'postprocess',
                    function (isSuccess, initial) {
                        arguments.callee.previousMethod.apply(this, arguments);

                        load = this.load.bind(this);
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'postprocess',
                    function (isSuccess, initial) {
                        arguments.callee.previousMethod.apply(this, arguments);

                        _.each(afterLoadListeners, function (listener) {
                            listener();
                        });
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'buildWidgetRequestURL',
                    function (params) {
                        if (!params) {
                            return arguments.callee.previousMethod.call(this, params);
                        }

                        var filtersString = paramsToQueryString(filtersToParamArray(params.cloudFilters));

                        return arguments.callee.previousMethod.call(this, _.omit(params, 'cloudFilters'))
                            + (filtersString.length > 0 ? '&' + filtersString : '');
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'load',
                    function (_params) {
                        var params = _.extend({}, _params, {cloudFilters: cloudFilters});

                        return arguments.callee.previousMethod.call(this, params);
                    }
                );

                decorateConcreteWidgetClasses(
                    decorateClasses,
                    'ProductsListView',
                    'postprocess',
                    function (isSuccess, initial) {
                        arguments.callee.previousMethod.apply(this, arguments);

                        if (isSuccess) {
                            if ($(this.base).hasClass('products-search-result')) {
                                // Workaround to run after skins/customer/items_list/product/search/controller.js
                                setTimeout(function () {
                                    var form = $('.search-product-form form');

                                    form.submit(function () {
                                        var query = form.find('input[name="substring"]').val(),
                                            categoryId = form.find('select[name="categoryId"]').val() || null,
                                            brand = form.find('select[name="brandId"]').val() || null,
                                            vendor = form.find('select[name="vendorId"]').val() || null;

                                        var searchInFields = {
                                            name: 'by_title',
                                            description: 'by_descr',
                                            sku: 'by_sku',
                                            tags: 'by_tag'
                                        };

                                        var searchIn = [];

                                        _.each(searchInFields, function (inputName, field) {
                                            if (form.find('input[name="' + inputName + '"]:checked').length > 0) {
                                                searchIn.push(field);
                                            }
                                        });

                                        _.each(searchListeners, function (listener) {
                                            listener({
                                                query: query,
                                                categoryId: categoryId,
                                                brand: brand > 0 ? brand : null,
                                                vendor: vendor > 0 ? vendor : null,
                                                searchIn: searchIn
                                            });
                                        });
                                    });
                                }, 0);
                            }
                        }
                    }
                );
            }
        );

        return {
            reload: function () {
                load();
            },
            setFilters: function (filters) {
                cloudFilters = filters;
            },
            subscribeToNewSearches: function (f) {
                searchListeners.push(f);
            },
            subscribeToAfterLoad: function (f) {
                afterLoadListeners.push(f);
            }
        };
    })();

    var facets = initialData.facets,
        filters = initialData.filters,
        stats = initialData.stats,
        numFound = initialData.numFound,
        filtersApi = initialData.filtersApi,
        currencyFormat = initialData.currencyFormat;

    filters.min_price = [
        typeof filters.min_price !== 'undefined' && !isNaN(parseFloat(filters.min_price[0]))
            ? parseFloat(filters.min_price[0])
            : null
    ];

    filters.max_price = [
        typeof filters.max_price !== 'undefined' && !isNaN(parseFloat(filters.max_price[0]))
            ? parseFloat(filters.max_price[0])
            : null
    ];

    var roundNumber = function (number, format) {
        return parseFloat(number.toFixed(format.numDecimals));
    };

    var formatPrice = function (price) {
        var formatNumber = function (number, format) {
            var n = number,
                c = format.numDecimals,
                d = format.decimalDelimiter,
                t = format.thousandsDelimiter,
                s = n < 0 ? "-" : "",
                i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                j = (j = i.length) > 3 ? j % 3 : 0;

            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };

        return currencyFormat.prefix + formatNumber(price, currencyFormat) + currencyFormat.suffix;
    };

    var TextValueRenderer = Vue.extend({
        props: ['value'],
        template: '#cloud-filters-text-value-renderer'
    });

    var ColorValueRenderer = Vue.extend({
        props: ['value'],
        template: '#cloud-filters-color-value-renderer',
        computed: {
            color: function () {
                return getColorByName(this.value) || getColorByName('white');
            },
            hasBorder: function () {
                return this.color === getColorByName('white');
            }
        }
    });

    var CategoryValueRenderer = Vue.extend({
        props: ['value'],
        template: '#cloud-filters-category-value-renderer',
        methods: {
            getCategoryName: function () {
                return this.value.path[this.value.path.length - 1];
            },
            getFullCategoryName: function (separator) {
                return this.value.path.join(separator);
            }
        }
    });

    var defaultFilterMixin = {
        data: function () {
            return {
                unfolded: false
            }
        },
        props: ['id', 'title', 'facet', 'toggledValues', 'onToggle'],
        methods: {
            isToggled: function (value) {
                return _.any(this.toggledValues, function (v) {
                    return _.isEqual(value, v);
                });
            },
            unfoldValues: function () {
                this.unfolded = true;
            },
            getValueRenderer: function () {
                return 'text-value-renderer';
            },
            getFilterValue: function (facetValue) {
                return facetValue;
            }
        },
        computed: {
            values: function () {
                return this.facet.counts;
            },
            foldedValues: function () {
                var folded = this.values.length <= initialData.maxFoldedFilterValues + 1
                    ? this.values
                    : this.values.slice(0, initialData.maxFoldedFilterValues);

                var numToggledInFolded = _.filter(folded, (function (v) {
                    return this.isToggled(this.getFilterValue(v.value));
                }).bind(this)).length;

                return this.unfolded || numToggledInFolded < this.toggledValues.length
                    ? this.values
                    : folded;
            },
            showUnfoldButton: function () {
                return this.values.length > this.foldedValues.length;
            },
            className: function () {
                // Replace spaces and special characters with dashes and lowercase
                return this.title.replace(/[!"#$%&'()*+,\-.\/:;<=>?@\[\\\]^`{|}~\s\t\n\v\f\r]+/g, '-').toLowerCase();
            }
        },
        template: '#cloud-filters-template-default',
        components: {
            'text-value-renderer': TextValueRenderer,
            'color-value-renderer': ColorValueRenderer,
            'category-value-renderer': CategoryValueRenderer
        }
    };

    var DefaultFilter = Vue.extend({
        mixins: [defaultFilterMixin]
    });

    var ColorFilter = Vue.extend({
        mixins: [defaultFilterMixin],
        methods: {
            getValueRenderer: function () {
                return 'color-value-renderer';
            }
        }
    });

    var SizeFilter = Vue.extend({
        mixins: [defaultFilterMixin],
        computed: {
            values: function () {
                this.facet.counts.sort(function (a, b) {
                    var av = a.value, bv = b.value;

                    var ai = sizes.indexOf(av);
                    var bi = sizes.indexOf(bv);

                    if (ai !== -1 && bi !== -1) {
                        return ai - bi;
                    } else if (ai !== -1 && bi === -1) {
                        return -1;
                    } else if (ai === -1 && bi !== -1) {
                        return 1;
                    } else if (av < bv) {
                        return -1;
                    } else if (av > bv) {
                        return 1;
                    } else {
                        return 0;
                    }
                });

                return this.facet.counts;
            }
        }
    });

    var CategoryFilter = Vue.extend({
        mixins: [defaultFilterMixin],
        methods: {
            getValueRenderer: function () {
                return 'category-value-renderer';
            },
            getFilterValue: function (facetValue) {
                return facetValue.id;
            }
        }
    });

    // Returns max rounding error in the form of 0.0049999... so that the error is maximum possible but doesn't allow
    // the sum number + error to round off to "number.005"
    function getMaxRoundingError(numDecimals, number) {
        var numDigits = Math.ceil(Math.log(number) / Math.log(10));

        // 15 is max significant digits for double precision floating point number
        var numNines = 15 - numDigits - numDecimals - 1;

        return (Math.pow(10, numNines + 1) / 2 - 1) / Math.pow(10, numNines + numDecimals + 1);
    }

    Vue.filter('priceFilterValue', {
        read: function (val) {
            // Convert to user currency

            return val !== null && val !== undefined
                ? formatPrice(roundNumber(val * currencyFormat.rate, currencyFormat))
                : val;
        },
        write: function (formatted, old, type) {
            // Convert from user currency

            var val = +formatted
                .replace(currencyFormat.thousandsDelimiter, '')
                .replace(currencyFormat.decimalDelimiter, '.')
                .replace(currencyFormat.prefix, '')
                .replace(currencyFormat.suffix, '')
                .replace(/[^\d.,]/g, '');

            if (!isNaN(val) && val !== 0) {
                val = roundNumber(val, currencyFormat);

                var error = currencyFormat.rate === 1 ?
                    0 : getMaxRoundingError(currencyFormat.numDecimals, val / currencyFormat.rate);

                return type === 'min'
                    ? (val - error) / currencyFormat.rate
                    : (val + error) / currencyFormat.rate;
            } else {
                return null;
            }
        }
    });

    var PriceFilter = Vue.extend({
        methods: {
            getMinPrice: function (defText) {
                return this.statsMin !== null ? formatPrice(this.statsMin * currencyFormat.rate) : defText;
            },
            getMaxPrice: function (defText) {
                return this.statsMax !== null ? formatPrice(this.statsMax * currencyFormat.rate) : defText;
            }
        },
        props: ['title', 'min', 'max', 'statsMin', 'statsMax'],
        template: '#cloud-filters-template-price'
    });

    var CloudFiltersComponent = Vue.extend({
        computed: {
            isAnyFilterSet: function () {
                return _.any(this.filters, function (fs) {
                    return (fs.length === 1 && fs[0] !== null) || fs.length > 1;
                });
            },
            isPriceFilterVisible: function () {
                var priceRange = this.stats.price,
                    zeroPrice = priceRange.min === 0 && priceRange.max === 0,
                    nullPrice = priceRange.min === null && priceRange.max === null,
                    priceFilterSet = this.filters.min_price[0] !== null || this.filters.max_price[0] !== null;

                return priceFilterSet || (!zeroPrice && !nullPrice);
            },
            isVisible: function () {
                if (!this.loaded) {
                    return false;
                }

                var isAnyFilterVisible = _.any(this.facets, (function (facet) {
                    if (this.isPriceFilter(facet)) {
                        return this.isPriceFilterVisible;
                    } else {
                        return facet.counts.length > 0;
                    }
                }).bind(this));

                return this.isAnyFilterSet || isAnyFilterVisible;
            }
        },

        watch: {
            'filters.min_price[0]': function (val, oldVal) {
                if (val !== oldVal && !(!val && !oldVal)) {
                    if (this.master) {
                        this.priceFilterChanged();
                    } else if (!this.priceSyncLocked) {
                        this.syncOtherInstanceMinPrice(val);
                    }
                }
            },
            'filters.max_price[0]': function (val, oldVal) {
                if (val !== oldVal && !(!val && !oldVal)) {
                    if (this.master) {
                        this.priceFilterChanged();
                    } else if (!this.priceSyncLocked) {
                        this.syncOtherInstanceMaxPrice(val);
                    }
                }
            },
            'isVisible': function () {
                this.setMobileFiltersLinkVisibility(this.isVisible);
            }
        },

        methods: {
            toggleFilterAction: function (fieldId, fieldValue, isToggled) {
                if (isToggled) {
                    this.filters[fieldId].push(fieldValue);
                } else {
                    Vue.set(
                        this.filters,
                        fieldId,
                        _.filter(this.filters[fieldId], function (f) {
                            return !_.isEqual(f, fieldValue);
                        })
                    );
                }

                this.replaceHistoryState();

                productList.setFilters(this.filters);

                this.fetchFacetsAndReload();
            },

            priceFilterChanged: function () {
                productList.setFilters(this.filters);

                this.replaceHistoryState();

                this.fetchFacetsAndReload();
            },

            resetFiltersAction: function () {
                this.clearFilters();

                this.replaceHistoryState();

                productList.setFilters(this.filters);

                this.fetchFacetsAndReload();
            },

            searchAction: function (params) {
                filtersApi.data.q = params.query;
                filtersApi.data.categoryId = params.categoryId;
                filtersApi.data.searchIn = params.searchIn;

                if (params.brand !== null) {
                    filtersApi.data.conditions.brand = [params.brand];
                } else {
                    delete filtersApi.data.conditions.brand;
                }

                if (params.vendor !== null) {
                    filtersApi.data.conditions.vendor = [params.vendor];
                } else {
                    delete filtersApi.data.conditions.vendor;
                }

                this.clearFilters();

                this.replaceHistoryState();

                productList.setFilters(this.filters);

                this.fetchFacetsAndReload();
            },

            afterReload: function () {
                this.setMobileFiltersLinkVisibility(this.isVisible);
            },

            setMobileFiltersLinkVisibility: function (visible) {
                $(mobileLinkSelector).toggleClass('hidden', !visible);
            },

            clearFilters: function () {
                this.priceSyncLocked = true;

                _.each(this.filters, (function (val, key) {
                    if (
                        key !== 'min_price'
                        && key !== 'max_price'
                    ) {
                        this.filters[key] = [];
                    } else {
                        this.filters[key] = [null];
                    }
                }).bind(this));
            },

            fetchFacets: function () {
                // Do not submit empty filters:
                var filters = _.clone(this.filters);

                _.each(filters, function (value, key) {
                    if (_.isEmpty(value)) {
                        delete filters[key];
                    }
                });

                var data = _.extend(
                    {},
                    filtersApi.data,
                    {filters: filters},
                    document.documentElement.lang ? {lang: document.documentElement.lang} : {}
                );

                return $.ajax(
                    filtersApi.url,
                    {
                        method: 'POST',
                        data: JSON.stringify(data),
                        // We use text/plain to avoid CORS preflight request
                        contentType: 'text/plain; charset=UTF-8',
                        dataType: 'json'
                    }
                )
                    .then((function (json) {
                        this.setFacets(json.facets, json.stats, json.numFoundProducts);
                    }).bind(this))
                    .then(this.syncOtherInstance.bind(this));
            },

            setFacets: function (facets, stats, numFound) {
                this.facets   = typeof filtersApi.data.conditions.stock_status === 'undefined' ? facets : _.omit(facets, 'availability');
                this.stats    = stats;
                this.numFound = numFound;

                this.loaded = !!this.facets;

                this.syncFiltersAndFacets();
            },

            fetchFacetsAndReload: function () {
                return this.fetchFacets()
                    .then(productList.reload);
            },

            syncOtherInstance: function () {
                var instance = this === CloudFiltersMobile ? CloudFiltersDesktop : CloudFiltersMobile;

                if (!instance) {
                    return;
                }

                instance.filters = deepClone(this.filters);

                instance.setFacets(
                    deepClone(this.facets),
                    deepClone(this.stats),
                    this.numFound
                );

                this.priceSyncLocked = false;
            },

            syncOtherInstanceMinPrice: function (price) {
                var instance = this === CloudFiltersMobile ? CloudFiltersDesktop : CloudFiltersMobile;

                if (!instance) {
                    return;
                }

                Vue.set(instance.filters, 'min_price', [price]);
            },

            syncOtherInstanceMaxPrice: function (price) {
                var instance = this === CloudFiltersMobile ? CloudFiltersDesktop : CloudFiltersMobile;

                if (!instance) {
                    return;
                }

                Vue.set(instance.filters, 'max_price', [price]);
            },

            replaceHistoryState: function () {
                var other = _.filter(queryStringToParams(), function (p) {
                        return p[0].indexOf(FILTER_PARAM_PREFIX) !== 0;
                    });

                var filters = filtersToParamArray(this.filters),
                    paramsStr = paramsToQueryString(other.concat(filters)),
                    query = paramsStr ? '?' + paramsStr : '';

                window.history.replaceState(
                    {cloudFilters: this.filters},
                    null,
                    window.location.pathname + query + window.location.hash
                );
            },

            replaceStateFromHistory: function (state) {
                this.filters = state.cloudFilters;

                this.fetchFacetsAndReload();
            },

            syncFiltersAndFacets: function () {
                // Create empty filters for all facets
                _.each(this.facets, (function (facet, fieldId) {
                    if (!(fieldId in this.filters)) {
                        Vue.set(this.filters, fieldId, []);
                    }
                }).bind(this));

                // Remove filters that don't have corresponding facets
                _.each(this.filters, (function (filterValues, filterId) {
                    if (
                        ((filterId === 'min_price' || filterId === 'max_price')
                            && !('price' in this.facets))
                        || (filterId !== 'min_price'
                            && filterId !== 'max_price'
                            && !(filterId in this.facets))
                    ) {
                        this.filters[filterId] = [];
                    }
                }).bind(this));
            },

            getFilterType: function (facet) {
                if (this.isColorFilter(facet)) {
                    return 'color-filter';
                } else if (this.isSizeFilter(facet)) {
                    return 'size-filter';
                } else if (this.isCategoryFilter(facet)) {
                    return 'category-filter';
                } else if (this.isPriceFilter(facet)) {
                    return 'price-filter';
                } else {
                    return 'default-filter';
                }
            },

            isCategoryFilter: function (facet) {
                return facet.type === 'category' || facet.type === 'custom_category';
            },

            isPriceFilter: function (facet) {
                return facet.type === 'price';
            },

            isSizeFilter: function (facet) {
                var sizeFilterNames = ['size'];

                var sizeishName = _.any(sizeFilterNames, function (v) {
                    return facet.name.toLowerCase().indexOf(v) !== -1;
                });

                if (!sizeishName) {
                    return false;
                }

                return _.all(facet.counts, function (c) {
                    return sizes.indexOf(c.value) !== -1;
                });
            },

            isColorFilter: function (facet) {
                var colorFilterNames = initialData.colorFilterNames;

                var colorishName = _.any(colorFilterNames, function (v) {
                    return facet.name.toLowerCase().indexOf(v) !== -1;
                });

                if (!colorishName) {
                    return false;
                }

                return _.any(facet.counts, function (c) {
                    return getColorByName(c.value) !== null;
                });
            }
        },

        components: {
            'default-filter': DefaultFilter,
            'color-filter': ColorFilter,
            'size-filter': SizeFilter,
            'category-filter': CategoryFilter,
            'price-filter': PriceFilter
        },

        created: function () {
            if (this.facets == null) {
                if (this.master) {
                    this.fetchFacets();
                }
            } else {
                this.loaded = true;

                this.syncFiltersAndFacets();
            }

            if (this.master) {
                $(window).on('popstate', (function (event) {
                    var state = event.originalEvent.state;

                    if (state && typeof state.cloudFilters !== 'undefined') {
                        this.replaceStateFromHistory(state);
                    }
                }).bind(this));

                productList.subscribeToNewSearches(this.searchAction.bind(this));

                productList.subscribeToAfterLoad(this.afterReload.bind(this));

                this.replaceHistoryState();

                productList.setFilters(this.filters);
            }
        }
    });

    var CloudFiltersData = {
        facets: typeof filtersApi.data.conditions.stock_status === 'undefined' ? facets : _.omit(facets, 'availability'),
        filters: filters,
        stats: stats,
        numFound: numFound,
        loaded: false,
        priceSyncLocked: false
    };

    var CloudFiltersDesktop = null;

    var CloudFiltersMobile = null;

    core.bind('cf-mobile.created', function () {
        CloudFiltersMobile = new CloudFiltersComponent({
            el: '#cloud-filters-mobile',
            data: deepClone(CloudFiltersData)
        });

        CloudFiltersDesktop = new CloudFiltersComponent({
            el: selector,
            data: _.extend({master: true}, CloudFiltersData)
        });
    });

    core.bind('cf-mobile.failed', function () {
        if (!CloudFiltersDesktop) {
            CloudFiltersDesktop = new CloudFiltersComponent({
                el: selector,
                data: _.extend({master: true}, CloudFiltersData)
            });
        }
    });

    function cfMobileMenu () {
        $(function () {
            var cfMobileMenu = $('#cf-slide-menu');

            cfMobileMenu.removeClass('hidden');

            cfMobileMenu.mmenu({
                extensions: ['pagedim-black'],
                navbar: {
                    add: true,
                    title: 'Filters'
                },
                navbars: [
                    {
                        position: "top",
                        content: ["prev", "title", "close"],
                        height: 1
                    }
                ],
                offCanvas: {
                    pageSelector: "#page-wrapper",
                    position: "right",
                    zposition: "front"
                }
            });

            var api = cfMobileMenu.data('mmenu');

            if (!_.isUndefined(api)) {
                core.trigger('cf-mobile.created', api);

                api.bind('closed', function () {
                    api.closeAllPanels();
                });
            } else {
                core.trigger('cf-mobile.failed');
            }
        });
    }

    core.autoload(cfMobileMenu);
})(_, jQuery);
