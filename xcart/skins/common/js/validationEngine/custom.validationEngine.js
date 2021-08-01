(function($){

  $.validationEngineLanguage.allRules.onlySmallLetterNumberUnder = {
    regex: /^[a-z][0-9a-z_]+$/,
    alertText: "* Only small letter, digits and undescore sign are allowed"
  };

  $.validationEngineLanguage.allRules.modifier = {
    regex: /^[\-\+]{1}([0-9]+)([\.,]([0-9]+))?([%]{1})?$/,
    alertText: "* Wrong format"
  };

  $.validationEngineLanguage.allRules.numberWithInfinity = {
    regex: /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+)|∞)?([\.]([0-9]+))?$/,
    alertText: "* Wrong format"
  };

  $.validationEngineLanguage.allRules.integerWithInfinity = {
    regex: /^[\-\+]?(\d+|∞)$/,
    alertText: "* Not a valid integer"
  };

  $.validationEngineLanguage.allRules.url = {
    regex: /^[a-z](?:[-a-z0-9\+\.])*:(?:\/\/(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:])*@)?(?:\[(?:(?:(?:[0-9a-f]{1,4}:){6}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|::(?:[0-9a-f]{1,4}:){5}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|(?:[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){4}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|(?:[0-9a-f]{1,4}:[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){3}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|(?:(?:[0-9a-f]{1,4}:){0,2}[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:){2}(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|(?:(?:[0-9a-f]{1,4}:){0,3}[0-9a-f]{1,4})?::[0-9a-f]{1,4}:(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|(?:(?:[0-9a-f]{1,4}:){0,4}[0-9a-f]{1,4})?::(?:[0-9a-f]{1,4}:[0-9a-f]{1,4}|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3})|(?:(?:[0-9a-f]{1,4}:){0,5}[0-9a-f]{1,4})?::[0-9a-f]{1,4}|(?:(?:[0-9a-f]{1,4}:){0,6}[0-9a-f]{1,4})?::)|v[0-9a-f]+[-a-z0-9\._~!\$&'\(\)\*\+,;=:]+)\]|(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}|(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=@])*)(?::[0-9]*)?(?:\/(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@]))*)*|\/(?:(?:(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@]))+)(?:\/(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@]))*)*)?|(?:(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@]))+)(?:\/(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@]))*)*|(?!(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@])))(?:\?(?:(?:%[0-9a-f][0-9a-f]|[-a-z0-9\._~\xA0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF!\$&'\(\)\*\+,;=:@])|[\/\?])*)?$/i,
    alertText: "* Invalid URL"
  };

  $.validationEngineLanguage.allRules.phone = {
    regex:/^([\+]?[\d]{1,3})?(?:\s*[ \(]([\d]{2,6})[ \)])?\s*([\d \.\-\/]{3,20})\s*(?:x|ext|extension|,)?\s*([\d]{1,4})?$/,
    alertText:"* Wrong number format"
  };

  $.validationEngineLanguage.allRules.email = {
    func: function (field, rules, i, options) {
      field.val($.trim(field.val()));

      var customRule = rules[i + 1];
      var rule = options.allrules[customRule];
      var pattern = new RegExp(rule.regex_old);

      return pattern.test(field.val())
    },
    regex: null,
    regex_old: $.validationEngineLanguage.allRules.email.regex,
    alertText: $.validationEngineLanguage.allRules.email.alertText,
  }

})(jQuery);
