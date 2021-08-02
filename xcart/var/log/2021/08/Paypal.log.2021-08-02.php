<?php die(); ?>
[10:52:21.000000] PaypalCommercePlatform Onboarding AccessToken:Retrieve access token
Runtime id: 3424da193860d4144c783446783a84aa
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php?target=onboarding_wizard
Method: GET

[10:52:24.000000] array (
  'message' => 'PaypalCommercePlatform Onboarding AccessToken:Access token recieved',
  'data' => 
  array (
    'scope' => 'https://uri.paypal.com/services/customer/partner-referrals/readwrite https://uri.paypal.com/services/payments/realtimepayment https://uri.paypal.com/services/disputes/update-seller https://uri.paypal.com/services/payments/payment/authcapture openid https://uri.paypal.com/services/disputes/read-seller https://uri.paypal.com/services/payments/refund https://uri.paypal.com/services/customer/onboarding/user https://uri.paypal.com/services/risk/raas/transaction-context https://uri.paypal.com/services/partners/merchant-accounts/readwrite https://uri.paypal.com/services/identity/grantdelegation https://api.paypal.com/v1/payments/.* https://uri.paypal.com/services/payments/referenced-payouts-items/readwrite https://uri.paypal.com/services/reporting/search/read https://uri.paypal.com/services/payments/initiatepayment https://uri.paypal.com/services/customer/onboarding/account https://uri.paypal.com/services/customer/partner https://uri.paypal.com/services/customer/onboarding/sessions https://uri.paypal.com/services/customer/merchant-integrations/read https://uri.paypal.com/services/applications/webhooks',
    'access_token' => 'A21AAOHiMyxmwEXc20SLKacdPRdJGYHN1_Ce5gHvfftd2LhuPR_oeppQBQRpReF_YG_tinzgCO75ROsvVD_s9XfDwTBHxP87Q',
    'token_type' => 'Bearer',
    'app_id' => 'APP-14G02482RW819934D',
    'expires_in' => 32400,
    'nonce' => '2021-08-02T08:51:56Zf6E5ABXlTrqop_WAGntT_WDz2nh2Gh_tTMl2iT5WlRw',
    'expiration' => 1627926656,
    'partner_id' => '5BPT2FEWWYATY',
  ),
)
Runtime id: 3424da193860d4144c783446783a84aa
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php?target=onboarding_wizard
Method: GET

[10:52:24.000000] array (
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
              'seller_nonce' => 'dffc2a4520b49dd8bb29ddd009d2df865da04fc715c1f73bed1a9bc0f695c4ec1d030b59ba5f4d81b7c3ce819513f51990b9add62ee045883b9fcc696f3687fc',
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
      'return_url' => 'http://192.168.220.1:8000/admin.php?target=paypal_commerce_platform_settings&action=onboarding_return&return=onboarding_wizard',
    ),
  ),
)
Runtime id: 3424da193860d4144c783446783a84aa
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php?target=onboarding_wizard
Method: GET

[10:52:26.000000] array (
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
     'body' => '{"name":"INVALID_REQUEST","message":"Request is not well-formed, syntactically incorrect, or violates schema.","debug_id":"29189ad935d97","information_link":"","details":[{"issue":"INVALID_PARAMETER_SYNTAX","description":"The value of a field does not conform to the expected format.","field":"/partner_config_override/return_url","location":"body"}],"links":[]}',
     'scheme' => 'https',
     'host' => 'api.paypal.com',
     'path' => '/v2/customer/partner-referrals',
     'uri' => 'https://api.paypal.com/v2/customer/partner-referrals',
     'port' => 443,
  ),
)
Runtime id: 3424da193860d4144c783446783a84aa
SAPI: fpm-fcgi; IP: 192.168.220.1
URI: /admin.php?target=onboarding_wizard
Method: GET

