<?php die(); ?>
[20:49:31.000000] PaypalCommercePlatform Onboarding AccessToken:Retrieve access token
Runtime id: 123392f9b9f95cd77c4ecb46f73bbe00
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[20:49:37.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding AccessToken:Access token recieved',
  'data' => 
  array (
    'scope' => 'https://uri.paypal.com/services/customer/partner-referrals/readwrite https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/customer/onboarding/user https://uri.paypal.com/services/risk/raas/transaction-context https://uri.paypal.com/services/partners/merchant-accounts/readwrite https://uri.paypal.com/services/identity/grantdelegation https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/referenced-payouts-items/readwrite https://uri.paypal.com/services/reporting/search/read https://uri.paypal.com/services/payments/initiatepayment https://uri.paypal.com/services/customer/onboarding/account https://uri.paypal.com/services/customer/partner https://uri.paypal.com/services/customer/onboarding/sessions https://uri.paypal.com/services/customer/merchant-integrations/read https://uri.paypal.com/services/applications/webhooks',
    'access_token' => 'A21AAOAlfRVj2D27kpS0EeARZKUSJ8PU7xhIJ2jlhM06_4blJMChhniYhyNtA-2v4foI3s5sl4bUrgHPkqiIBoXiBVKuOmyMg',
    'token_type' => 'Bearer',
    'app_id' => 'APP-14G02482RW819934D',
    'expires_in' => 32400,
    'nonce' => '2021-08-05T12:14:40ZnBy3VDV2RqecCmrQYnTH8lhGbeMfheiBxYFvmhyw6Xw',
    'expiration' => 1628198020,
    'partner_id' => '5BPT2FEWWYATY',
  ),
)
Runtime id: 123392f9b9f95cd77c4ecb46f73bbe00
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[20:49:37.000000] array (
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
Runtime id: 123392f9b9f95cd77c4ecb46f73bbe00
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

[20:49:38.000000] array (
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
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"558d88d1b64c6","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: 123392f9b9f95cd77c4ecb46f73bbe00
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php
Method: GET

