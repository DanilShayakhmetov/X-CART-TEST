/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Cloud
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('cloud', [], function () {

  var checker = function () {
    return new Promise(function (resolve, reject) {
      core.post(
        {
          target: 'cloud',
          action: 'check'
        },
        function (xhr, status, data, valid) {
          resolve(JSON.parse(data))
        },
        {}
      )
    })
  }

  var check = function () {
    setTimeout(function () {
      checker().then(function (data) {
        if (data.status) {
          if (data.status === 'error') {
            console.log('error')
          } else {
            console.log('in-progress')
            check()
          }
        } else if (data.url && data.xid) {
          console.log('https://' + data.url + '/admin.php?xid=' + data.xid)
          window.location.replace('https://' + data.url + '/admin.php?xid=' + data.xid);
        }
      })
    }, 5000)
  }

  check()

})