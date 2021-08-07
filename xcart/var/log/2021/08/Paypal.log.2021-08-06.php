<?php die(); ?>
[13:13:09.000000] PaypalCommercePlatform Onboarding AccessToken:Retrieve access token
Runtime id: 3fecbbca166f6fcd27baafca80a9d6ab
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:13:11.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding AccessToken:Access token recieved',
  'data' => 
  array (
    'scope' => 'https://uri.paypal.com/services/customer/partner-referrals/readwrite https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/customer/onboarding/user https://uri.paypal.com/services/risk/raas/transaction-context https://uri.paypal.com/services/partners/merchant-accounts/readwrite https://uri.paypal.com/services/identity/grantdelegation https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/referenced-payouts-items/readwrite https://uri.paypal.com/services/reporting/search/read https://uri.paypal.com/services/payments/initiatepayment https://uri.paypal.com/services/customer/onboarding/account https://uri.paypal.com/services/customer/partner https://uri.paypal.com/services/customer/onboarding/sessions https://uri.paypal.com/services/customer/merchant-integrations/read https://uri.paypal.com/services/applications/webhooks',
    'access_token' => 'A21AAMx_wrFkUWfyCJb87lIOavJdUzb4ImS7bSFckTlCB0GyZLU0A9pPMUXSY0vdEyxBHi7d63bCrq8BZioaMn_edlp8OHGcA',
    'token_type' => 'Bearer',
    'app_id' => 'APP-14G02482RW819934D',
    'expires_in' => 32400,
    'nonce' => '2021-08-06T06:46:43ZdNvEte-uSQMJ_5Jb3YyZyhAHZYE8VP6Q2BRETs0PQrQ',
    'expiration' => 1628264743,
    'partner_id' => '5BPT2FEWWYATY',
  ),
)
Runtime id: 3fecbbca166f6fcd27baafca80a9d6ab
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:13:11.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link',
  'data' => 
  array (
    'operations' => 
    array (
      0 => 
      array (
        'operation' => 'API_INTEGRATION',
        'api_integration_preference' => 
        array (
          'rest_api_integration' => 
          array (
            'integration_method' => 'PAYPAL',
            'integration_type' => 'FIRST_PARTY',
            'first_party_details' => 
            array (
              'features' => 
              array (
                0 => 'PAYMENT',
                1 => 'REFUND',
                2 => 'ACCESS_MERCHANT_INFORMATION',
              ),
              'seller_nonce' => '06e86c69ea5817878afa508f0607a8514d784d94a4454a8189ae02e8e4852cc94ef1bce2f65d3a0f9a410a6b5eade5106a166aafc1261d05b9d83310e01974af',
            ),
          ),
        ),
      ),
    ),
    'products' => 
    array (
      0 => 'PPCP',
    ),
    'legal_consents' => 
    array (
      0 => 
      array (
        'type' => 'SHARE_DATA_CONSENT',
        'granted' => true,
      ),
    ),
    'partner_config_override' => 
    array (
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return',
    ),
  ),
)
Runtime id: 3fecbbca166f6fcd27baafca80a9d6ab
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:13:12.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link error',
  'data' => 
  (object) array(
     '__CLASS__' => 'PEAR2\\HTTP\\Request\\Response',
     'code' => 400,
     'headers' => 
    (object) array(
       '__CLASS__' => 'PEAR2\\HTTP\\Request\\Headers',
       'iterationStyle' => 'lowerCase',
       'fields:protected' => 'Array(13)',
       'camelCase:protected' => NULL,
       'lowerCase:protected' => NULL,
    ),
     'cookies' => 
    array (
    ),
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"868c2474d38c8","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: 3fecbbca166f6fcd27baafca80a9d6ab
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:25:38.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link',
  'data' => 
  array (
    'operations' => 
    array (
      0 => 
      array (
        'operation' => 'API_INTEGRATION',
        'api_integration_preference' => 
        array (
          'rest_api_integration' => 
          array (
            'integration_method' => 'PAYPAL',
            'integration_type' => 'FIRST_PARTY',
            'first_party_details' => 
            array (
              'features' => 
              array (
                0 => 'PAYMENT',
                1 => 'REFUND',
                2 => 'ACCESS_MERCHANT_INFORMATION',
              ),
              'seller_nonce' => '06e86c69ea5817878afa508f0607a8514d784d94a4454a8189ae02e8e4852cc94ef1bce2f65d3a0f9a410a6b5eade5106a166aafc1261d05b9d83310e01974af',
            ),
          ),
        ),
      ),
    ),
    'products' => 
    array (
      0 => 'PPCP',
    ),
    'legal_consents' => 
    array (
      0 => 
      array (
        'type' => 'SHARE_DATA_CONSENT',
        'granted' => true,
      ),
    ),
    'partner_config_override' => 
    array (
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return',
    ),
  ),
)
Runtime id: 5f7ab59891b700a82e45bcffc29f3554
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:25:39.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link error',
  'data' => 
  (object) array(
     '__CLASS__' => 'PEAR2\\HTTP\\Request\\Response',
     'code' => 400,
     'headers' => 
    (object) array(
       '__CLASS__' => 'PEAR2\\HTTP\\Request\\Headers',
       'iterationStyle' => 'lowerCase',
       'fields:protected' => 'Array(12)',
       'camelCase:protected' => NULL,
       'lowerCase:protected' => NULL,
    ),
     'cookies' => 
    array (
    ),
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"b8eb5e3fedba","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: 5f7ab59891b700a82e45bcffc29f3554
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:27:38.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link',
  'data' => 
  array (
    'operations' => 
    array (
      0 => 
      array (
        'operation' => 'API_INTEGRATION',
        'api_integration_preference' => 
        array (
          'rest_api_integration' => 
          array (
            'integration_method' => 'PAYPAL',
            'integration_type' => 'FIRST_PARTY',
            'first_party_details' => 
            array (
              'features' => 
              array (
                0 => 'PAYMENT',
                1 => 'REFUND',
                2 => 'ACCESS_MERCHANT_INFORMATION',
              ),
              'seller_nonce' => '06e86c69ea5817878afa508f0607a8514d784d94a4454a8189ae02e8e4852cc94ef1bce2f65d3a0f9a410a6b5eade5106a166aafc1261d05b9d83310e01974af',
            ),
          ),
        ),
      ),
    ),
    'products' => 
    array (
      0 => 'PPCP',
    ),
    'legal_consents' => 
    array (
      0 => 
      array (
        'type' => 'SHARE_DATA_CONSENT',
        'granted' => true,
      ),
    ),
    'partner_config_override' => 
    array (
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return',
    ),
  ),
)
Runtime id: bb7d742e2aad5bcb4444b14ee48f8e2f
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:27:38.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link error',
  'data' => 
  (object) array(
     '__CLASS__' => 'PEAR2\\HTTP\\Request\\Response',
     'code' => 400,
     'headers' => 
    (object) array(
       '__CLASS__' => 'PEAR2\\HTTP\\Request\\Headers',
       'iterationStyle' => 'lowerCase',
       'fields:protected' => 'Array(13)',
       'camelCase:protected' => NULL,
       'lowerCase:protected' => NULL,
    ),
     'cookies' => 
    array (
    ),
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"4e0c6832d34fd","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: bb7d742e2aad5bcb4444b14ee48f8e2f
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:49:28.000000] PaypalCommercePlatform Onboarding AccessToken:Retrieve access token
Runtime id: 36ad76d73f04a1c56dfa762dfe60d0ad
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:49:31.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding AccessToken:Access token recieved',
  'data' => 
  array (
    'scope' => 'https://uri.paypal.com/services/customer/partner-referrals/readwrite https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/customer/onboarding/user https://uri.paypal.com/services/risk/raas/transaction-context https://uri.paypal.com/services/partners/merchant-accounts/readwrite https://uri.paypal.com/services/identity/grantdelegation https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/referenced-payouts-items/readwrite https://uri.paypal.com/services/reporting/search/read https://uri.paypal.com/services/payments/initiatepayment https://uri.paypal.com/services/customer/onboarding/account https://uri.paypal.com/services/customer/partner https://uri.paypal.com/services/customer/onboarding/sessions https://uri.paypal.com/services/customer/merchant-integrations/read https://uri.paypal.com/services/applications/webhooks',
    'access_token' => 'A21AAMx_wrFkUWfyCJb87lIOavJdUzb4ImS7bSFckTlCB0GyZLU0A9pPMUXSY0vdEyxBHi7d63bCrq8BZioaMn_edlp8OHGcA',
    'token_type' => 'Bearer',
    'app_id' => 'APP-14G02482RW819934D',
    'expires_in' => 32400,
    'nonce' => '2021-08-06T06:46:43ZdNvEte-uSQMJ_5Jb3YyZyhAHZYE8VP6Q2BRETs0PQrQ',
    'expiration' => 1628264743,
    'partner_id' => '5BPT2FEWWYATY',
  ),
)
Runtime id: 36ad76d73f04a1c56dfa762dfe60d0ad
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:49:31.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link',
  'data' => 
  array (
    'operations' => 
    array (
      0 => 
      array (
        'operation' => 'API_INTEGRATION',
        'api_integration_preference' => 
        array (
          'rest_api_integration' => 
          array (
            'integration_method' => 'PAYPAL',
            'integration_type' => 'FIRST_PARTY',
            'first_party_details' => 
            array (
              'features' => 
              array (
                0 => 'PAYMENT',
                1 => 'REFUND',
                2 => 'ACCESS_MERCHANT_INFORMATION',
              ),
              'seller_nonce' => '06e86c69ea5817878afa508f0607a8514d784d94a4454a8189ae02e8e4852cc94ef1bce2f65d3a0f9a410a6b5eade5106a166aafc1261d05b9d83310e01974af',
            ),
          ),
        ),
      ),
    ),
    'products' => 
    array (
      0 => 'PPCP',
    ),
    'legal_consents' => 
    array (
      0 => 
      array (
        'type' => 'SHARE_DATA_CONSENT',
        'granted' => true,
      ),
    ),
    'partner_config_override' => 
    array (
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return',
    ),
  ),
)
Runtime id: 36ad76d73f04a1c56dfa762dfe60d0ad
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[13:49:33.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link error',
  'data' => 
  (object) array(
     '__CLASS__' => 'PEAR2\\HTTP\\Request\\Response',
     'code' => 400,
     'headers' => 
    (object) array(
       '__CLASS__' => 'PEAR2\\HTTP\\Request\\Headers',
       'iterationStyle' => 'lowerCase',
       'fields:protected' => 'Array(12)',
       'camelCase:protected' => NULL,
       'lowerCase:protected' => NULL,
    ),
     'cookies' => 
    array (
    ),
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"f0dd684be5352","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: 36ad76d73f04a1c56dfa762dfe60d0ad
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[14:23:43.000000] PaypalCommercePlatform Onboarding AccessToken:Retrieve access token
Runtime id: e09d9b706b51041f3d0b09835cb6a7d6
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[14:23:44.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding AccessToken:Access token recieved',
  'data' => 
  array (
    'scope' => 'https://uri.paypal.com/services/customer/partner-referrals/readwrite https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/customer/onboarding/user https://uri.paypal.com/services/risk/raas/transaction-context https://uri.paypal.com/services/partners/merchant-accounts/readwrite https://uri.paypal.com/services/identity/grantdelegation https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/referenced-payouts-items/readwrite https://uri.paypal.com/services/reporting/search/read https://uri.paypal.com/services/payments/initiatepayment https://uri.paypal.com/services/customer/onboarding/account https://uri.paypal.com/services/customer/partner https://uri.paypal.com/services/customer/onboarding/sessions https://uri.paypal.com/services/customer/merchant-integrations/read https://uri.paypal.com/services/applications/webhooks',
    'access_token' => 'A21AAMx_wrFkUWfyCJb87lIOavJdUzb4ImS7bSFckTlCB0GyZLU0A9pPMUXSY0vdEyxBHi7d63bCrq8BZioaMn_edlp8OHGcA',
    'token_type' => 'Bearer',
    'app_id' => 'APP-14G02482RW819934D',
    'expires_in' => 32400,
    'nonce' => '2021-08-06T06:46:43ZdNvEte-uSQMJ_5Jb3YyZyhAHZYE8VP6Q2BRETs0PQrQ',
    'expiration' => 1628264743,
    'partner_id' => '5BPT2FEWWYATY',
  ),
)
Runtime id: e09d9b706b51041f3d0b09835cb6a7d6
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[14:23:44.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link',
  'data' => 
  array (
    'operations' => 
    array (
      0 => 
      array (
        'operation' => 'API_INTEGRATION',
        'api_integration_preference' => 
        array (
          'rest_api_integration' => 
          array (
            'integration_method' => 'PAYPAL',
            'integration_type' => 'FIRST_PARTY',
            'first_party_details' => 
            array (
              'features' => 
              array (
                0 => 'PAYMENT',
                1 => 'REFUND',
                2 => 'ACCESS_MERCHANT_INFORMATION',
              ),
              'seller_nonce' => '06e86c69ea5817878afa508f0607a8514d784d94a4454a8189ae02e8e4852cc94ef1bce2f65d3a0f9a410a6b5eade5106a166aafc1261d05b9d83310e01974af',
            ),
          ),
        ),
      ),
    ),
    'products' => 
    array (
      0 => 'PPCP',
    ),
    'legal_consents' => 
    array (
      0 => 
      array (
        'type' => 'SHARE_DATA_CONSENT',
        'granted' => true,
      ),
    ),
    'partner_config_override' => 
    array (
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return',
    ),
  ),
)
Runtime id: e09d9b706b51041f3d0b09835cb6a7d6
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[14:23:45.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link error',
  'data' => 
  (object) array(
     '__CLASS__' => 'PEAR2\\HTTP\\Request\\Response',
     'code' => 400,
     'headers' => 
    (object) array(
       '__CLASS__' => 'PEAR2\\HTTP\\Request\\Headers',
       'iterationStyle' => 'lowerCase',
       'fields:protected' => 'Array(13)',
       'camelCase:protected' => NULL,
       'lowerCase:protected' => NULL,
    ),
     'cookies' => 
    array (
    ),
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"3fecd792289fb","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: e09d9b706b51041f3d0b09835cb6a7d6
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[15:03:34.000000] PaypalCommercePlatform Onboarding AccessToken:Retrieve access token
Runtime id: 280518027879b4b9474d6dabe39b52e0
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[15:03:34.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding AccessToken:Access token recieved',
  'data' => 
  array (
    'scope' => 'https://uri.paypal.com/services/customer/partner-referrals/readwrite https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/customer/onboarding/user https://uri.paypal.com/services/risk/raas/transaction-context https://uri.paypal.com/services/partners/merchant-accounts/readwrite https://uri.paypal.com/services/identity/grantdelegation https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/referenced-payouts-items/readwrite https://uri.paypal.com/services/reporting/search/read https://uri.paypal.com/services/payments/initiatepayment https://uri.paypal.com/services/customer/onboarding/account https://uri.paypal.com/services/customer/partner https://uri.paypal.com/services/customer/onboarding/sessions https://uri.paypal.com/services/customer/merchant-integrations/read https://uri.paypal.com/services/applications/webhooks',
    'access_token' => 'A21AAMx_wrFkUWfyCJb87lIOavJdUzb4ImS7bSFckTlCB0GyZLU0A9pPMUXSY0vdEyxBHi7d63bCrq8BZioaMn_edlp8OHGcA',
    'token_type' => 'Bearer',
    'app_id' => 'APP-14G02482RW819934D',
    'expires_in' => 32400,
    'nonce' => '2021-08-06T06:46:43ZdNvEte-uSQMJ_5Jb3YyZyhAHZYE8VP6Q2BRETs0PQrQ',
    'expiration' => 1628264743,
    'partner_id' => '5BPT2FEWWYATY',
  ),
)
Runtime id: 280518027879b4b9474d6dabe39b52e0
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[15:03:34.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link',
  'data' => 
  array (
    'operations' => 
    array (
      0 => 
      array (
        'operation' => 'API_INTEGRATION',
        'api_integration_preference' => 
        array (
          'rest_api_integration' => 
          array (
            'integration_method' => 'PAYPAL',
            'integration_type' => 'FIRST_PARTY',
            'first_party_details' => 
            array (
              'features' => 
              array (
                0 => 'PAYMENT',
                1 => 'REFUND',
                2 => 'ACCESS_MERCHANT_INFORMATION',
              ),
              'seller_nonce' => '06e86c69ea5817878afa508f0607a8514d784d94a4454a8189ae02e8e4852cc94ef1bce2f65d3a0f9a410a6b5eade5106a166aafc1261d05b9d83310e01974af',
            ),
          ),
        ),
      ),
    ),
    'products' => 
    array (
      0 => 'PPCP',
    ),
    'legal_consents' => 
    array (
      0 => 
      array (
        'type' => 'SHARE_DATA_CONSENT',
        'granted' => true,
      ),
    ),
    'partner_config_override' => 
    array (
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return',
    ),
  ),
)
Runtime id: 280518027879b4b9474d6dabe39b52e0
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[15:03:35.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding:Generate SignUp link error',
  'data' => 
  (object) array(
     '__CLASS__' => 'PEAR2\\HTTP\\Request\\Response',
     'code' => 400,
     'headers' => 
    (object) array(
       '__CLASS__' => 'PEAR2\\HTTP\\Request\\Headers',
       'iterationStyle' => 'lowerCase',
       'fields:protected' => 'Array(12)',
       'camelCase:protected' => NULL,
       'lowerCase:protected' => NULL,
    ),
     'cookies' => 
    array (
    ),
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"47ef8769d40de","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: 280518027879b4b9474d6dabe39b52e0
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

