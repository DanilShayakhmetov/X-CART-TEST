/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Translator
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var Translator = Object.extend({
  languageLabels: [],

  constructor: function TranslatorConstructor() {
    this.loadPreloadedLabels();
  },

  loadPreloadedLabels: function () {
    this.loadLanguageHash(
        window.xlite_preloaded_labels
    );
  },

  translate: function(label, params)
  {
    var translation = this._findInLoaded(label);

    if (translation === null) {
      translation = this._loadLabel(label);

      if (translation) {
        this._saveToLoaded(label, translation);
      }
    }

    if (params) {
      translation = this._processTranslationParams(translation, params)
    }

    return translation;
  },


  loadLanguageHash: function(hash)
  {
    _.each(
        hash,
        _.bind(
            function (translation, label) {
              var foundTranslation = this._findInLoaded(label);

              if (foundTranslation === null) {
                this._saveToLoaded(label, translation);
              }
            },
            this
        )
    );
  },

  _processTranslationParams: function(translation, params) {
    _.each(params, function (paramValue, paramName) {
      translation = translation.replace('{{' + paramName + '}}', paramValue);
    });

    return translation;
  },

  _findInLoaded: function(label) {
    var translation = null;

    for (var i = 0; i < this.languageLabels.length; i++) {
      if (this.languageLabels[i].name == label) {
        translation = this.languageLabels[i].label;
        break;
      }
    }

    return translation;
  },

  _saveToLoaded: function(label, translation) {
    this.languageLabels.push(
        {
          name:  label,
          label: translation
        }
    );
  },

  _loadLabel: function(label) {
    return core.rest.get('translation', label, false);
  },

});
