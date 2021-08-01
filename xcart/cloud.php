<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

require_once __DIR__ . '/service/src/XCart/ConfigParser/ConfigParserException.php';
require_once __DIR__ . '/service/src/XCart/ConfigParser/ConfigMissingFileException.php';
require_once __DIR__ . '/service/src/XCart/ConfigParser/ConfigWrongFormattedFileException.php';
require_once __DIR__ . '/service/src/XCart/ConfigParser/ConfigFile.php';
require_once __DIR__ . '/service/src/XCart/ConfigParser/ConfigPostProcessor.php';
require_once __DIR__ . '/service/src/XCart/ConfigParser/ConfigParser.php';

function getPlanName($xcnPlan): string
{
    $plans = [
        '6' => 'CloudEssentials',
        '7' => 'CloudBusiness',
        '8' => 'CloudPremium',
    ];

    return $plans[$xcnPlan] ?? '';
}

function findInstance(string $url, string $businessName): array
{
    $query = <<<GRAPHQL
query (\$businessName: String!) {
    gate_api_search_by_business_name(business_name: \$businessName)
}
GRAPHQL;

    $variables = [
        'businessName' => $businessName,
    ];

    $response = request($url, $query, $variables);

    return $response['data']['gate_api_search_by_business_name'] ?? [];
}

function checkInstance(string $url, string $uuid): array
{
    $query = <<<GRAPHQL
query (\$uuid: ID!) {
    gate_api_check(uuid: \$uuid) {
        uuid
        business_name
        email
        account_email
        domain_name
        plan
        license
        created_at
        updated_at
        status
        url
        xid
    }
}
GRAPHQL;

    $variables = [
        'uuid' => $uuid,
    ];

    $response = request($url, $query, $variables);

    return $response['data']['gate_api_check'] ?? [];
}

function createInstance(string $url, string $email, string $businessName, string $license, string $plan, string $domain): string
{
    $query = <<<GRAPHQL
mutation (\$instance: InputCloudInstance) {
  gate_api_transfer(instance: \$instance)
}
GRAPHQL;

    $variables = [
        'instance' => [
            'email'         => $email,
            'account_email' => $email,
            'business_name' => $businessName,
            'domain_name'   => $domain,
            'plan'          => $plan,
            'license'       => $license,
        ],
    ];

    $response = request($url, $query, $variables);

    if (isset($response['errors'])) {
        throw new \Exception($response['errors'][0]['message']);
    }

    return $response['data']['gate_api_transfer'] ?? '';
}

function changeDomain(string $url, string $uuid, string $domainName): string
{
    $query = <<<GRAPHQL
mutation (\$uuid: ID! \$domainName: String!) {
  gate_api_change_domain(uuid: \$uuid domain_name: \$domainName)
}
GRAPHQL;

    $variables = [
        'uuid'       => $uuid,
        'domainName' => $domainName,
    ];

    $response = request($url, $query, $variables);

    if (isset($response['errors'])) {
        throw new \Exception($response['errors'][0]['message']);
    }

    return $response['data']['gate_api_change_domain'] ?? '';
}

function request(string $url, string $query, array $variables = []): array
{
    $curl = curl_init();

    $data = [
        'query' => $query,
    ];

    if ($variables) {
        $data['variables'] = $variables;
    }

    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);

    curl_close($curl);

    return $response ? @json_decode($response, true) : [];
}

function response(array $data, int $code): void
{
    http_response_code($code);
    header('Content-type: application/json');

    echo json_encode($data);
    exit();
}

$config = new XCart\ConfigParser\ConfigParser($_SERVER, __DIR__ . '/etc/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $authCode = $input['auth_code'] ?? '';

    if (empty($authCode) || $authCode !== $config->getOption('installer_details', 'auth_code')) {
        response([
            'error' => 'Request must contain correct `auth_code` field',
        ], 400);
    }

    $action = $input['action'] ?? '';
    $domain = $input['domain'] ?? '';

    if (empty($action)) {
        response([
            'error' => 'Request must contain `action` field',
        ], 400);
    }

    $url          = $config->getOption('service', 'gate_api_url');
    $businessName = $config->getOption('service', 'business_name');

    if ($action === 'transfer') {
        $email   = $config->getOption('service', 'cloud_account_email');
        $isCloud = $config->getOption('service', 'is_cloud');
        $isTrial = $config->getOption('service', 'is_trial');

        $license = '';
        $xcnPlan = '';

        $licenseData = [];
        $licenseFile = __DIR__ . '/files/service/licenseStorage.data';
        if (is_readable($licenseFile)) {
            $licenseData = @unserialize(file_get_contents($licenseFile), []);
        }
        foreach ($licenseData as $licenseDatum) {
            if ($licenseDatum['name'] === 'Core' && $licenseDatum['author'] === 'CDev') {
                $license = $licenseDatum['keyValue'];
                $xcnPlan = $licenseDatum['xcnPlan'];
            }
        }

        if ($license && $email && $businessName && $isCloud && !$isTrial) {
            try {
                [$uuid, $code] = ($uuids = findInstance($url, $businessName))
                    ? [$uuids[0], 200]
                    : [createInstance($url, $email, $businessName, $license, getPlanName($xcnPlan), $domain), 201];

                response(['instance' => checkInstance($url, $uuid)], $code);

            } catch (\Exception $e) {
                response(['error' => $e->getMessage()], 406);
            }
        }
    } elseif ($action === 'change_domain') {
        $uuids = findInstance($url, $businessName);
        if ($uuids) {
            try {
                $instance = checkInstance($url, $uuids[0]);

                $code = 200;

                if ($instance['status'] === 'transfered' && $instance['domain_name'] !== $domain) {
                    $code = 201;
                    changeDomain($url, $uuids[0], $domain);

                    $instance = checkInstance($url, $uuids[0]);
                }

                response(['instance' => $instance], $code);

            } catch (\Exception $e) {
                response(['error' => $e->getMessage()], 406);
            }
        }
    }

    response(['error' => 'Missing data'], 400);
} else {
    $action   = $_REQUEST['action'] ?? '';
    $domain   = $_REQUEST['domain'] ?? '';
    $authCode = $_REQUEST['auth_code'] ?? '';

    if (empty($authCode) || $authCode !== $config->getOption('installer_details', 'auth_code')) {
        http_response_code(400);
        header('Content-type: application/json');

        echo 'Request must contain correct `auth_code` field';
        exit();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <style>
      html {
        min-height: 100%
      }

      body {
        margin: 0;
        min-height: 100vh;
        font-family: Open Sans, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #000
      }

      *, :after, :before {
        box-sizing: border-box
      }

      @font-face {
        font-family: Open Sans;
        font-weight: 400;
        font-display: swap;
        src: url(https://www.x-cart.com/wp-content/themes/miniflat/build/fonts/open-sans/Regular/OpenSans-Regular.woff2) format("woff2"), url(https://www.x-cart.com/wp-content/themes/miniflat/build/fonts/open-sans/Regular/OpenSans-Regular.woff) format("woff")
      }

      @font-face {
        font-family: Open Sans;
        font-weight: 700;
        font-display: swap;
        src: url(https://www.x-cart.com/wp-content/themes/miniflat/build/fonts/open-sans/Semibold/OpenSans-Semibold.woff2) format("woff2"), url(https://www.x-cart.com/wp-content/themes/miniflat/build/fonts/open-sans/Semibold/OpenSans-Semibold.woff) format("woff")
      }

      .content {
        width: 100%;
        max-width: 750px;
        margin: auto
      }

      .content, h1 {
        font-weight: 400
      }

      h1 {
        line-height: 1.66;
        margin: 35px 0 0;
        font-size: 22px
      }

      p {
        margin: 35px 0 0;
        font-size: 14px;
        line-height: 24px
      }

      a, a:link, a:visited {
        color: #0F8DD0;
        text-decoration: none
      }

      a:active, a:focus, a:hover {
        text-decoration: underline
      }

      img, svg {
        max-width: 100%;
        height: auto
      }

      .top-menu {
        width: 100%;
        height: 60px;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: center;
        justify-content: center;
        -ms-flex-align: center;
        align-items: center;
        padding: 0 20px;
        justify-content: space-between;
        position: absolute;
        background: #fff;
        z-index: 2
      }

      .page {
        display: flex;
        flex-wrap: nowrap;
        min-height: 100%;
        padding-top: 60px;
        position: absolute;
        z-index: 1;
        width: 100%
      }

      .left-menu {
        width: 226px;
        flex: 0 0 226px;
        padding: 40px 22px 32px 22px;
        background: #253238
      }

      .left-menu img, .left-menu svg {
        display: block
      }

      .left-menu img + img, .left-menu svg + svg {
        margin-top: 30px
      }

      .left-menu__top {
        height: calc(100% - 120px);
        min-height: 330px
      }

      .left-menu__bottom {
        height: 120px
      }

      .main {
        flex: 1 1 100%;
        border-top: 1px solid #c1c1c2;
        font-size: 14px;
        line-height: 20px;
        text-align: center;
        padding: 40px 20px;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-pack: center;
        justify-content: center;
        -ms-flex-align: center;
        align-items: center
      }

      .phones {
        display: block;
        margin: 20px -15px -10px -15px
      }

      .phones__phone {
        display: inline-block;
        margin: 10px 15px;
        white-space: nowrap
      }

      .phones__phone a, .phones__phone a:link, .phones__phone a:visited {
        color: inherit;
        font-weight: 700;
        text-decoration: none
      }

      .phones__phone a:active, .phones__phone a:focus, .phones__phone a:hover {
        color: inherit;
        font-weight: 700;
        text-decoration: underline
      }

      .phones__phone a {
        display: block;
        line-height: 20px;
        background: none no-repeat left center;
        background-size: 20px auto;
        padding-left: 26px
      }

      .phones__phone--us a {
        background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiPjxkZWZzPjxjaXJjbGUgaWQ9ImEiIGN4PSIxMCIgY3k9IjEwIiByPSIxMCIvPjwvZGVmcz48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxtYXNrIGlkPSJiIiBmaWxsPSIjZmZmIj48dXNlIHhsaW5rOmhyZWY9IiNhIi8+PC9tYXNrPjx1c2UgZmlsbD0iI0Q4RDhEOCIgeGxpbms6aHJlZj0iI2EiLz48ZyBmaWxsLXJ1bGU9Im5vbnplcm8iIG1hc2s9InVybCgjYikiPjxwYXRoIGZpbGw9IiNDQjAwMUMiIGQ9Ik0tNS43OSAxOC45NDdoMzUuMjYzdjEuNTc5SC01Ljc5eiIvPjxwYXRoIGZpbGw9IiNGRkYiIGQ9Ik0tNS43OSAxNy4zNjhoMzUuMjYzdjEuNTc5SC01Ljc5eiIvPjxwYXRoIGZpbGw9IiNDQjAwMUMiIGQ9Ik0tNS43OSAxNS43ODloMzUuMjYzdjEuNTc5SC01Ljc5eiIvPjxwYXRoIGZpbGw9IiNGRkYiIGQ9Ik0tNS43OSAxNC4yMWgzNS4yNjN2MS41NzlILTUuNzl6Ii8+PHBhdGggZmlsbD0iI0NCMDAxQyIgZD0iTS01Ljc5IDEyLjYzMWgzNS4yNjN2MS41NzlILTUuNzl6Ii8+PHBhdGggZmlsbD0iI0ZGRiIgZD0iTS01Ljc5IDExLjA1MmgzNS4yNjN2MS41NzlILTUuNzl6Ii8+PHBhdGggZmlsbD0iI0NCMDAxQyIgZD0iTS01Ljc5IDkuNDczaDM1LjI2M3YxLjU3OUgtNS43OXoiLz48cGF0aCBmaWxsPSIjRkZGIiBkPSJNLTUuNzkgNy44OTVoMzUuMjYzdjEuNTc5SC01Ljc5eiIvPjxwYXRoIGZpbGw9IiNDQjAwMUMiIGQ9Ik0tNS43OSA2LjMxNmgzNS4yNjN2MS41NzlILTUuNzl6Ii8+PHBhdGggZmlsbD0iI0ZGRiIgZD0iTS01Ljc5IDQuNzM3aDM1LjI2M3YxLjU3OUgtNS43OXoiLz48cGF0aCBmaWxsPSIjQ0IwMDFDIiBkPSJNLTUuNzkgMy4xNThoMzUuMjYzdjEuNTc5SC01Ljc5eiIvPjxwYXRoIGZpbGw9IiNGRkYiIGQ9Ik0tNS43OSAxLjU3OWgzNS4yNjN2MS41NzlILTUuNzl6Ii8+PHBhdGggZmlsbD0iI0NCMDAxQyIgZD0iTS01Ljc5IDBoMzUuMjYzdjEuNTc5SC01Ljc5eiIvPjxwYXRoIGZpbGw9IiMyQTM1NjAiIGQ9Ik0tNS43OSAwaDE2LjMxNnYxMS4wNTNILTUuNzl6Ii8+PHBhdGggZmlsbD0iI0ZGRiIgZD0iTTEuMDQ5IDEuMDUybC4xMjYuNDA0aC40MDNsLS4zMjcuMjQ1LjEyNi40MDQtLjMyOC0uMjUyLS4zMjIuMjUyLjEyNy0uNDA0LS4zMjgtLjI0NWguNDAzem0wIDIuMTA2bC4xMjYuNDA0aC40MDNsLS4zMjcuMjUxLjEyNi4zOTctLjMyOC0uMjQ1LS4zMjIuMjQ1LjEyNy0uMzk3LS4zMjgtLjI1MWguNDAzem0wIDEuNTc5bC4xMjYuNDAzaC40MDNsLS4zMjcuMjQ1LjEyNi40MDQtLjMyOC0uMjUxLS4zMjIuMjUxLjEyNy0uNDA0LS4zMjgtLjI0NWguNDAzem0wIDIuMTA1bC4xMjYuNDA0aC40MDNsLS4zMjcuMjUxLjEyNi4zOTgtLjMyOC0uMjQ1LS4zMjIuMjQ1LjEyNy0uMzk4LS4zMjgtLjI1MWguNDAzem0wIDEuNTc5bC4xMjYuMzk3aC40MDNsLS4zMjcuMjUyLjEyNi40MDMtLjMyOC0uMjUxLS4zMjIuMjUxLjEyNy0uNDAzLS4zMjgtLjI1MmguNDAzem0xLjA1My02LjMxNmwuMTI2LjQwNGguNDAzbC0uMzI4LjI1MS4xMjYuMzk4LS4zMjctLjI0NS0uMzIyLjI0NS4xMi0uMzk4LS4zMjItLjI1MWguNDA0em0wIDEuNTc5bC4xMjYuMzk3aC40MDNsLS4zMjguMjUyLjEyNi40MDQtLjMyNy0uMjUyLS4zMjIuMjUyLjEyLS40MDQtLjMyMi0uMjUyaC40MDR6bTAgMi4xMDVsLjEyNi40MDRoLjQwM2wtLjMyOC4yNTIuMTI2LjM5Ny0uMzI3LS4yNDUtLjMyMi4yNDUuMTItLjM5Ny0uMzIyLS4yNTJoLjQwNHptMCAxLjU3OWwuMTI2LjM5N2guNDAzbC0uMzI4LjI1Mi4xMjYuNDA0LS4zMjctLjI1Mi0uMzIyLjI1Mi4xMi0uNDA0LS4zMjItLjI1MmguNDA0em0xLjU4NS02LjMxNmwuMTI2LjQwNGguMzk3bC0uMzIxLjI0NS4xMTkuNDA0LS4zMjEtLjI1Mi0uMzI4LjI1Mi4xMjYtLjQwNC0uMzI4LS4yNDVoLjQwNHptMCAyLjEwNmwuMTI2LjQwNGguMzk3bC0uMzIxLjI1MS4xMTkuMzk3LS4zMjEtLjI0NS0uMzI4LjI0NS4xMjYtLjM5Ny0uMzI4LS4yNTFoLjQwNHptMCAxLjU3OWwuMTI2LjQwM2guMzk3bC0uMzIxLjI0NS4xMTkuNDA0LS4zMjEtLjI1MS0uMzI4LjI1MS4xMjYtLjQwNC0uMzI4LS4yNDVoLjQwNHptMCAyLjEwNWwuMTI2LjQwNGguMzk3bC0uMzIxLjI1MS4xMTkuMzk4LS4zMjEtLjI0NS0uMzI4LjI0NS4xMjYtLjM5OC0uMzI4LS4yNTFoLjQwNHptMCAxLjU3OWwuMTI2LjM5N2guMzk3bC0uMzIxLjI1Mi4xMTkuNDAzLS4zMjEtLjI1MS0uMzI4LjI1MS4xMjYtLjQwMy0uMzI4LS4yNTJoLjQwNHptMS4wNTItNi4zMTZsLjEyLjQwNGguNDA0bC0uMzIyLjI1MS4xMi4zOTgtLjMyMi0uMjQ1LS4zMjcuMjQ1LjEyNi0uMzk4LS4zMjgtLjI1MWguNDAzem0wIDEuNTc5bC4xMi4zOTdoLjQwNGwtLjMyMi4yNTIuMTIuNDA0LS4zMjItLjI1Mi0uMzI3LjI1Mi4xMjYtLjQwNC0uMzI4LS4yNTJoLjQwM3ptMCAyLjEwNWwuMTIuNDA0aC40MDRsLS4zMjIuMjUyLjEyLjM5Ny0uMzIyLS4yNDUtLjMyNy4yNDUuMTI2LS4zOTctLjMyOC0uMjUyaC40MDN6bTAgMS41NzlsLjEyLjM5N2guNDA0bC0uMzIyLjI1Mi4xMi40MDQtLjMyMi0uMjUyLS4zMjcuMjUyLjEyNi0uNDA0LS4zMjgtLjI1MmguNDAzem0xLjA1LTYuMzE2bC4xMjUuNDA0aC40MDFsLS4zMjYuMjQ1LjEyNi40MDQtLjMyNi0uMjUyLS4zMjYuMjUyLjEyNS0uNDA0LS4zMjUtLjI0NWguNDAxem0wIDIuMTA2bC4xMjUuNDA0aC40MDFsLS4zMjYuMjUxLjEyNi4zOTctLjMyNi0uMjQ1LS4zMjYuMjQ1LjEyNS0uMzk3LS4zMjUtLjI1MWguNDAxem0wIDEuNTc5bC4xMjUuNDAzaC40MDFsLS4zMjYuMjQ1LjEyNi40MDQtLjMyNi0uMjUxLS4zMjYuMjUxLjEyNS0uNDA0LS4zMjUtLjI0NWguNDAxem0wIDIuMTA1bC4xMjUuNDA0aC40MDFsLS4zMjYuMjUxLjEyNi4zOTgtLjMyNi0uMjQ1LS4zMjYuMjQ1LjEyNS0uMzk4LS4zMjUtLjI1MWguNDAxem0wIDEuNTc5bC4xMjUuMzk3aC40MDFsLS4zMjYuMjUyLjEyNi40MDMtLjMyNi0uMjUxLS4zMjYuMjUxLjEyNS0uNDAzLS4zMjUtLjI1MmguNDAxem0xLjU3OS02LjMxNmwuMTI1LjQwNGguNDAxbC0uMzI2LjI1MS4xMjYuMzk4LS4zMjYtLjI0NS0uMzI2LjI0NS4xMjUtLjM5OC0uMzI1LS4yNTFoLjQwMXptMCAxLjU3OWwuMTI1LjM5N2guNDAxbC0uMzI2LjI1Mi4xMjYuNDA0LS4zMjYtLjI1Mi0uMzI2LjI1Mi4xMjUtLjQwNC0uMzI1LS4yNTJoLjQwMXptMCAyLjEwNWwuMTI1LjQwNGguNDAxbC0uMzI2LjI1Mi4xMjYuMzk3LS4zMjYtLjI0NS0uMzI2LjI0NS4xMjUtLjM5Ny0uMzI1LS4yNTJoLjQwMXptMCAxLjU3OWwuMTI1LjM5N2guNDAxbC0uMzI2LjI1Mi4xMjYuNDA0LS4zMjYtLjI1Mi0uMzI2LjI1Mi4xMjUtLjQwNC0uMzI1LS4yNTJoLjQwMXptMS4wNDktNi4zMTZsLjEyNi40MDRoLjQwNGwtLjMyOC4yNDUuMTI2LjQwNC0uMzI4LS4yNTItLjMyMS4yNTIuMTItLjQwNC0uMzIyLS4yNDVoLjQwNHptMCAyLjEwNmwuMTI2LjQwNGguNDA0bC0uMzI4LjI1MS4xMjYuMzk3LS4zMjgtLjI0NS0uMzIxLjI0NS4xMi0uMzk3LS4zMjItLjI1MWguNDA0em0wIDEuNTc5bC4xMjYuNDAzaC40MDRsLS4zMjguMjQ1LjEyNi40MDQtLjMyOC0uMjUxLS4zMjEuMjUxLjEyLS40MDQtLjMyMi0uMjQ1aC40MDR6bTAgMi4xMDVsLjEyNi40MDRoLjQwNGwtLjMyOC4yNTEuMTI2LjM5OC0uMzI4LS4yNDUtLjMyMS4yNDUuMTItLjM5OC0uMzIyLS4yNTFoLjQwNHptMCAxLjU3OWwuMTI2LjM5N2guNDA0bC0uMzI4LjI1Mi4xMjYuNDAzLS4zMjgtLjI1MS0uMzIxLjI1MS4xMi0uNDAzLS4zMjItLjI1MmguNDA0eiIvPjwvZz48L2c+PC9zdmc+)
      }

      .phones__phone--uk a {
        background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiPjxkZWZzPjxjaXJjbGUgaWQ9ImEiIGN4PSIxMCIgY3k9IjEwIiByPSIxMCIvPjwvZGVmcz48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxtYXNrIGlkPSJiIiBmaWxsPSIjZmZmIj48dXNlIHhsaW5rOmhyZWY9IiNhIi8+PC9tYXNrPjx1c2UgZmlsbD0iI0Q4RDhEOCIgeGxpbms6aHJlZj0iI2EiLz48ZyBmaWxsLXJ1bGU9Im5vbnplcm8iIG1hc2s9InVybCgjYikiPjxwYXRoIGZpbGw9IiNGRkYiIGQ9Ik0tNy4zNjggMGgzNC4yMTF2MjBILTcuMzY4eiIvPjxwYXRoIGZpbGw9IiNCRDAwMzQiIGQ9Ik0xNS41MjYgNi44NDJMMjYuODQzLjgzNlYwaC0uNzgzTDEzLjE1OCA2Ljg0MnptLS43ODkgNi4zMTZsMTIuMTA2IDYuMzE2di0xLjI2MmwtOS42ODctNS4wNTR6TS03LjM2OCAxLjI5NEwyLjg2NiA2Ljg0MmgyLjM5OEwtNy4zNjggMHpNNC40OSAxMy4xNThsLTExLjg1OCA2LjcyMVYyMGgyLjE0MWwxMi4wNy02Ljg0MnoiLz48cGF0aCBmaWxsPSIjMUEyMzdCIiBkPSJNMjQuNzM3IDBIMTIuNjMydjYuMzE2ek03LjM2OSAwSC00LjczNkw3LjM2OSA2LjMxNnptMTkuNDc0IDcuMzY4VjIuNjMybC04LjQyMiA0LjczNnptMCAxMC41Mjd2LTQuNzM3aC04LjQyMnpNLTQuMjEgMjBINi44NDN2LTYuMzE2em0xNi44NDIgMGgxMS4wNTNsLTExLjA1My02LjMxNnptLTIwLTYuODQydjQuNzM3bDguNDIxLTQuNzM3em0wLTUuNzloOC40MjFsLTguNDIxLTQuNzM2eiIvPjxwYXRoIGZpbGw9IiNCRDAwMzQiIGQ9Ik04LjI5NCAwdjguNDE2SC03LjM2OHYzLjU2M0g4LjI5NFYyMGgzLjE3NHYtOC4wMjFoMTUuMzc1VjguNDE2SDExLjQ2OFYweiIvPjwvZz48L2c+PC9zdmc+)
      }

      @media (max-width: 389px) {
        .top-menu svg:last-child {
          display: none
        }
      }

      @media (min-width: 544px) {
        .main {
          padding-left: 30px;
          padding-right: 30px
        }
      }

      @media (min-width: 640px) and (max-width: 799px) {
        h1 > span {
          white-space: nowrap
        }
      }

      @media (min-width: 768px) {
        h1 {
          font-size: 28px
        }
      }

      @media (max-width: 799px) {
        .left-menu {
          display: none
        }
      }

      .spinner {
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;

        font-size: 3px;
        text-indent: -9999em;
        border-top: 1.1em solid rgba(26, 189, 196, 0.2);
        border-right: 1.1em solid rgba(26, 189, 196, 0.2);
        border-bottom: 1.1em solid rgba(26, 189, 196, 0.2);
        border-left: 1.1em solid #1abdc4;
        transform: translateZ(0);
        animation: load8 1.1s infinite linear;
      }

      .spinner,
      .spinner:after {
        border-radius: 50%;
        width: 10em;
        height: 10em;
      }

      @keyframes load8 {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }
    </style>

    <script>
      var action = '<?php echo $action; ?>'
      var domain = '<?php echo $domain; ?>'
      var authCode = '<?php echo $authCode; ?>'

      var sendRequest = function (data) {
        return new Promise(function (resolve, reject) {

          var request = new XMLHttpRequest()
          request.open('POST', 'cloud.php', true)
          request.setRequestHeader('accept', 'application/json')

          request.onreadystatechange = function () {
            if (request.readyState === XMLHttpRequest.DONE) {
              var status = request.status
              if (status >= 200 && status < 400) {
                resolve(request)
              } else {
                reject(request)
              }
            }
          }

          request.send(JSON.stringify(data))
        })
      }

      var checker = function () {
        return new Promise(function (resolve, reject) {
          sendRequest({action: action, domain: domain, auth_code: authCode})
            .then(function (data) {
              resolve(JSON.parse(data.responseText))
            })
            .catch(function (data) {
              reject((data && data.responseText) ? JSON.parse(data.responseText) : null)
            })
        })
      }

      var check = function () {
        setTimeout(function () {
          checker().then(function (data) {
            if (data.instance) {
              if (data.instance.status === 'error') {
                console.log('error')
                window.location.replace('admin.php?target=cloud_domain_name&action=transfered&result=error')
              } else if (data.instance.status === 'transfered') {
                console.log('transfered')
                window.location.replace(`admin.php?target=cloud_domain_name&action=transfered&result=success&domain=${domain}`)
              } else {
                console.log('in-progress')
                check()
              }
            }
          }).catch(function (data) {
            if (data && data.error) {
              window.location.replace(`admin.php?target=cloud_domain_name&action=transfered&result=error&error_code=${data.error}`)
            } else {
              check()
            }
          })
        }, 5000)
      }

      check()

    </script>
</head>
<body>
<div class="top-menu">
    <svg aria-label="X-Cart logo" width="139" height="30" xmlns="http://www.w3.org/2000/svg">
        <g fill="none" fill-rule="evenodd">
            <path d="M135 18a4 4 0 110 8H43a4 4 0 110-8h92zM85 3a4 4 0 110 8H43a4 4 0 110-8h42z" fill="#C1C1C2"/>
            <g fill-rule="nonzero">
                <path d="M3.994 0h22.012A3.984 3.984 0 0130 3.994v22.012A3.984 3.984 0 0126.006 30H3.994A3.984 3.984 0 010 26.006V3.994A4.012 4.012 0 013.994 0"
                      fill="#E78A2F"/>
                <path d="M9.171 7v5.682c0 .328.063.492.295.718.38.37 1.433 1.415 1.813 1.785.21.205.506.307.78.307h5.901c.274 0 .57-.082.78-.307.38-.37 1.433-1.416 1.813-1.785.232-.226.295-.39.295-.718V7h2.15v6.933c.02.246-.106.493-.274.657l-2.003 1.948 1.982 1.929c.168.164.295.41.295.656v3.426c0 .39-.085.43-.485.43h-1.665v-2.584c0-.328-.063-.492-.295-.718-.38-.37-1.434-1.415-1.813-1.785a1.135 1.135 0 00-.78-.307H12.06c-.274 0-.57.082-.78.307-.38.37-1.434 1.416-1.813 1.785-.232.226-.295.39-.295.718v2.133c0 .37-.105.472-.506.472H7v-3.877c0-.246.126-.492.295-.656l2.002-1.95c-.695-.676-1.56-1.517-1.98-1.948a.956.956 0 01-.296-.656V7h2.15zm3.846 4.1v1.454h-1.504v-1.455h1.504zm6.017 0v1.454H17.53v-1.455h1.504zm-3.008 0v1.454H14.52v-1.455h1.505zm-3.009-2.91v1.455h-1.504V8.19h1.504zm6.017 0v1.455H17.53V8.19h1.504zm-3.008 0v1.455H14.52V8.19h1.505z"
                      fill="#FFF"/>
                <path d="M8.955 22c.713.151 1.265.755 1.265 1.49 0 .82-.736 1.51-1.633 1.51-.76 0-1.403-.496-1.587-1.165h1.288c.368 0 .667-.281.667-.626V22zm14.78 0c.713.15 1.265.75 1.265 1.479-.023.835-.736 1.521-1.633 1.521-.759 0-1.403-.493-1.587-1.157h1.288c.368 0 .667-.279.667-.622V22z"
                      fill="#A1482C"/>
            </g>
        </g>
    </svg>
    <svg width="100" height="14" viewBox="0 0 100 14">
        <rect x="0" y="0" width="100" height="14" fill="#e9ecef" stroke="none" rx="6" ry="6"></rect>
    </svg>
</div>
<div class="page">
    <div class="left-menu" role="decoration" aria-hidden="true">
        <div class="left-menu__top">
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
        </div>
        <div class="left-menu__bottom">
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" width="140" height="20" viewBox="0 0 140 20">
                <path fill="#4D585F" fill-rule="evenodd"
                      d="M14 0a6 6 0 016 6v8a6 6 0 01-6 6H6a6 6 0 01-6-6V6a6 6 0 016-6h8zm121 5a5 5 0 110 10H45a5 5 0 110-10h90z"/>
            </svg>
        </div>
    </div>
    <div class="main">
        <div class="content">
            <svg xmlns="http://www.w3.org/2000/svg" aria-label="Developer" shape-rendering="geometricPrecision"
                 text-rendering="geometricPrecision" viewBox="0 0 300 225" width="300" height="225">
                <style>
                  @keyframes evdw1kaeakn919_to__to {
                    0%, 20%, 50%, to {
                      transform: translate(0, -2px)
                    }
                    10%, 33% {
                      transform: translate(0, 0)
                    }
                  }

                  @keyframes evdw1kaeakn920_to__to {
                    0%, 20%, 50%, 81% {
                      transform: translate(0, 0)
                    }
                    10% {
                      transform: translate(0, -5px)
                    }
                    33% {
                      transform: translate(0, -3px)
                    }
                    65%, to {
                      transform: translate(0, -2px)
                    }
                  }

                  @keyframes evdw1kaeakn921_to__to {
                    0%, 20%, 50%, 81% {
                      transform: translate(0, 0)
                    }
                    10%, 65% {
                      transform: translate(0, -5px)
                    }
                    33% {
                      transform: translate(0, -3px)
                    }
                    to {
                      transform: translate(0, -2px)
                    }
                  }

                  @keyframes evdw1kaeakn922_to__to {
                    0%, 33%, to {
                      transform: translate(0, 0)
                    }
                    10% {
                      transform: translate(3px, 0)
                    }
                  }

                  @keyframes evdw1kaeakn923_to__to {
                    0%, 50%, to {
                      transform: translate(0, 0)
                    }
                    20% {
                      transform: translate(-3px, 0)
                    }
                  }

                  @keyframes evdw1kaeakn925_to__to {
                    0%, 20%, 50%, to {
                      transform: translate(0, -2px)
                    }
                    10%, 33% {
                      transform: translate(0, 0)
                    }
                  }

                  @keyframes evdw1kaeakn926_to__to {
                    0%, 20%, 50%, 81% {
                      transform: translate(0, 0)
                    }
                    10% {
                      transform: translate(0, -5px)
                    }
                    33% {
                      transform: translate(0, -3px)
                    }
                    65%, to {
                      transform: translate(0, -2px)
                    }
                  }

                  @keyframes evdw1kaeakn927_to__to {
                    0%, 20%, 50%, 81% {
                      transform: translate(0, 0)
                    }
                    10% {
                      transform: translate(0, -5px)
                    }
                    33% {
                      transform: translate(0, -3px)
                    }
                    65%, to {
                      transform: translate(0, -2px)
                    }
                  }

                  @keyframes evdw1kaeakn928_to__to {
                    0%, 20%, 50%, to {
                      transform: translate(0, -2px)
                    }
                    10%, 33% {
                      transform: translate(0, 0)
                    }
                  }

                  @keyframes evdw1kaeakn929_to__to {
                    0%, 44%, to {
                      transform: translate(0, 0)
                    }
                    20% {
                      transform: translate(-2px, 0)
                    }
                  }

                  @keyframes evdw1kaeakn931_to__to {
                    0%, to {
                      transform: translate(-1px, 0)
                    }
                    38% {
                      transform: translate(1px, 0)
                    }
                    67% {
                      transform: translate(0, 0)
                    }
                  }

                  @keyframes evdw1kaeakn932_tr__tr {
                    0%, to {
                      transform: translate(0, 0) rotate(0deg)
                    }
                    38% {
                      transform: translate(0, 0) rotate(-6deg)
                    }
                    50% {
                      transform: translate(0, 0) rotate(-3deg)
                    }
                  }

                  @keyframes evdw1kaeakn933_to__to {
                    0%, 38%, 67%, to {
                      transform: translate(0, 0)
                    }
                  }

                  @keyframes evdw1kaeakn933_tr__tr {
                    0%, 38%, to {
                      transform: rotate(0deg)
                    }
                    67% {
                      transform: rotate(-1deg)
                    }
                  }

                  @keyframes evdw1kaeakn934_to__to {
                    0%, to {
                      transform: translate(-1px, 0)
                    }
                    38% {
                      transform: translate(1px, 0)
                    }
                    67% {
                      transform: translate(0, 0)
                    }
                  }</style>
                <g id="evdw1kaeakn92">
                    <g id="evdw1kaeakn93" fill-rule="evenodd" transform="translate(104 .25)">
                        <path id="evdw1kaeakn94" fill="#CEE1EB" stroke="none" stroke-width="1"
                              d="M70.348 73.75c16.079 0 40.008 2.234 49.968 6.957s15.529 10.907 15.107 13.58c-.122.768-.247 1.533-.377 2.294 2.957 4.825 6.952 12.508 9.558 22.7 6.108 23.885 8.527 50.069 8.39 66.178-.109 12.875-19.874 10.383-21.53 8.883-.83-.752-3.079-2.295-6.745-4.629-1.665 1.844-4.634 3.41-8.938 4.063-11.36 1.722-30.545-.361-47.84-.086-17.62.28-34.428 1.352-43.484-.244-.688 3.094-1.632 5.016-2.89 5.26-16.742 3.246-4.556-1.201-11.153-11.639-5.31-8.4-12.228-67.307-9.977-87.355.026-1.26.234-2.482.626-3.666.323-1.255.725-2.147 1.213-2.617l-.03.032c.77-1.305 1.787-2.56 3.05-3.762C20.871 74.853 54.77 73.75 70.347 73.75z"/>
                        <path id="evdw1kaeakn95" fill="#8B9EA8" stroke="#ADC5D2" stroke-width="3"
                              d="M132.59 194.436c9.675 0 26.41 1.876 26.41-3.544s-13.997-16.142-23.672-16.142S120 183.836 120 189.256s2.916 5.18 12.59 5.18z"/>
                        <path id="evdw1kaeakn96" fill="#8B9EA8" stroke="#ADC5D2" stroke-width="3"
                              d="M33.59 194.436c9.675 0 26.41 1.876 26.41-3.544S46.003 174.75 36.328 174.75 21 183.836 21 189.256s2.916 5.18 12.59 5.18z"/>
                        <path id="evdw1kaeakn97" fill="#ADC5D2" stroke="none" stroke-width="1"
                              d="M116.814 106.092c2.255 7.844 2.255 27.74 2.921 45.531.666 17.792-.129 31.059-.172 31.126-.042.067-3.063-12.806 0-30.856.762-4.487-6.274-13.483-6.536-31.615-.262-18.132 1.405 11.976 3.615 8.816 1.284-1.836-2.083-30.845.172-23.002z"/>
                        <g id="evdw1kaeakn98" transform="translate(34 -.25)">
                            <path id="evdw1kaeakn99" fill="#ADC5D2" stroke="none" stroke-width="1"
                                  d="M8.017 61.468c7.35-6.925 43.011-5.245 50.655 0 7.644 5.245 9.865 13.57 7.158 18.5-2.708 4.93-18 20.62-31.369 22.605C21.093 104.558 2.842 84.185.911 79.968c-1.932-4.218-.244-11.575 7.106-18.5z"/>
                            <path id="evdw1kaeakn910" fill="#8B9EA8" stroke="none" stroke-width="1"
                                  d="M12.026 61.989c5.017-3.677 33.57-3.45 41.426-.215 7.856 3.235 7.193 10.3 4.185 18.194-3.009 7.893-14.079 19.225-24.524 19.225-10.444 0-21.135-13.06-24.829-19.225-3.694-6.165-1.275-14.302 3.742-17.98z"/>
                            <path id="evdw1kaeakn911" fill="#FFF" stroke="none" stroke-width="1"
                                  d="M6.794 75.858c2.354-.327 3.925-.54 4.714-.642 9.236-1.183 16.62.414 22.046.414 5.592 0 12.878-1.872 21.834-1.244.655.046 1.949.145 3.88.297-.82 3.576-2.02 6.716-3.6 9.42-1.58 2.704-3.973 5.406-7.18 8.107-3.852 4.655-8.83 6.983-14.934 6.983-6.105 0-11.116-2.328-15.034-6.983-1.97-1.098-4.416-3.686-7.34-7.763-2.924-4.078-4.386-6.94-4.386-8.589z"/>
                            <path id="evdw1kaeakn912" fill="#F1BD9A" stroke="none" stroke-width="1"
                                  d="M32.541 11.44h.69c10.074.026 17.64 1.223 19.216 11.15.087.547.089 7.796.068 15.162 1.373-1.155 2.921-1.847 4.102-1.847 2.714 0 2.457 3.653 2.457 8.16s-2.2 8.16-4.913 8.16c-.6 0-1.174-.178-1.705-.504l-.009 1.862v.11c0 2.47-.584 4.86-1.56 7.09v13.333c1.848.062 3.348.152 4.5.27-.724 10.67-7.613 16.235-20.666 16.697-13.053.462-20.9-4.816-23.54-15.834 1.148-.163 2.64-.31 4.477-.443V60.315c-1.285-2.439-2.088-5.073-2.088-7.796 0-.097-.012-.438-.032-.98-.604.442-1.272.686-1.974.686-2.713 0-4.912-3.653-4.912-8.16s-.257-8.16 2.456-8.16c1.11 0 2.546.612 3.855 1.644-.326-7.951-.647-16.195-.572-16.72 1.445-10.09 9.939-9.406 20.15-9.39z"/>
                            <path id="evdw1kaeakn913" fill="none" stroke="#E0A57D" stroke-linecap="round"
                                  stroke-linejoin="round" stroke-width="2"
                                  d="M17.414 61.61c3.745 7.332 9.168 10.998 16.268 10.998 7.1 0 12.426-3.666 15.976-10.998"/>
                            <path id="evdw1kaeakn914" fill="#2E2F2F" stroke="none" stroke-width="1"
                                  d="M32.073.046C48.41.622 50.624 5.097 51.91 9.272c.623 2.024 5.835-1.73 7.001 5.19.577 3.417-.247 13.75-3.5 20.181-.34.671-.923 4.515-1.75 11.533h-2.334c.138-2.08-.056-4.58-.583-7.496-.79-4.375-2.917-5.766-2.334-8.65.583-2.883 1.167-5.766-1.167-7.496-.657-.486-7.01-2.112-14.785-2.306-8.131-.203-17.702 1.062-18.47 2.306-2.208 3.582 0 3.46 0 7.496 0 2.153-1.166 5.186-1.166 9.226 0 2.694.389 5.385 1.167 8.073h-2.334c-.739-3.048-1.322-4.97-1.75-5.766-1.534-2.854-2.334-3.46-3.5-5.766C5.236 33.49 3.068 21.01 8.736 9.272c2.337-4.84 7-9.803 23.336-9.226z"/>
                        </g>
                    </g>
                    <g id="evdw1kaeakn915" transform="translate(1 39.25)">
                        <g id="evdw1kaeakn916" transform="translate(0 .75)">
                            <path id="evdw1kaeakn917" fill="#EBEEF1" fill-rule="evenodd" stroke="#64676D"
                                  stroke-width="2"
                                  d="M264.356 154.364H125.402c-.628 0-1.196.254-1.607.665-.412.412-.666.98-.666 1.607 0 .628.254 1.196.666 1.607.411.412.98.666 1.607.666h138.954c.628 0 1.196-.254 1.607-.666.412-.41.666-.979.666-1.607 0-.627-.254-1.195-.666-1.607a2.266 2.266 0 00-1.607-.665z"/>
                            <g id="evdw1kaeakn918" transform="matrix(-1 0 0 1 171.325 138.182)">
                                <g transform="translate(0 -2)"
                                   style="animation:evdw1kaeakn919_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn919" width="7.82" height="18" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(14.277 -1)"/>
                                </g>
                                <g style="animation:evdw1kaeakn920_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn920" width="7.82" height="16.545" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(6.003 .455)"/>
                                </g>
                                <g style="animation:evdw1kaeakn921_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn921" width="7.82" height="13.636" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(22.552 3.364)"/>
                                </g>
                                <g style="animation:evdw1kaeakn922_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn922" width="7.82" height="10.727" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(30.827 6.273)"/>
                                </g>
                                <g style="animation:evdw1kaeakn923_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn923" width="7.82" height="9.273" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(-1 7.727)"/>
                                </g>
                            </g>
                            <g id="evdw1kaeakn924" transform="translate(216.705 138.182)">
                                <g transform="translate(0 -2)"
                                   style="animation:evdw1kaeakn925_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn925" width="7.82" height="18" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(15.277 -1)"/>
                                </g>
                                <g style="animation:evdw1kaeakn926_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn926" width="7.82" height="16.545" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(7.003 .455)"/>
                                </g>
                                <g style="animation:evdw1kaeakn927_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn927" width="7.82" height="13.636" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(23.552 3.364)"/>
                                </g>
                                <g transform="translate(0 -2)"
                                   style="animation:evdw1kaeakn928_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn928" width="7.82" height="10.727" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(31.827 6.273)"/>
                                </g>
                                <g style="animation:evdw1kaeakn929_to__to 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn929" width="7.82" height="9.273" fill="#F1BD9A"
                                          fill-rule="evenodd" stroke="#64676D" stroke-width="2" rx="3.91" ry="0"
                                          transform="translate(-1 7.727)"/>
                                </g>
                            </g>
                            <g id="evdw1kaeakn930" transform="translate(153.684)">
                                <g transform="translate(-1)"
                                   style="animation:evdw1kaeakn931_to__to 1000ms linear infinite normal forwards">
                                    <ellipse id="evdw1kaeakn931" fill="#2E2F2F" fill-rule="evenodd" stroke="none"
                                             stroke-width="1" rx="2.183" ry="2.182" transform="translate(5.093 2.182)"/>
                                </g>
                                <g style="animation:evdw1kaeakn932_tr__tr 1000ms linear infinite normal forwards">
                                    <rect id="evdw1kaeakn932" width="11.64" height="1.455" fill="#2E2F2F"
                                          fill-rule="evenodd" stroke="none" stroke-width="1" rx=".727" ry="0"
                                          transform="translate(19.642)"/>
                                </g>
                                <g style="animation:evdw1kaeakn933_to__to 1000ms linear infinite normal forwards">
                                    <g style="animation:evdw1kaeakn933_tr__tr 1000ms linear infinite normal forwards">
                                        <rect id="evdw1kaeakn933" width="11.64" height="1.455" fill="#E0A57D"
                                              fill-rule="evenodd" stroke="none" stroke-width="1" rx=".727" ry="0"
                                              transform="translate(10.185 19.636)"/>
                                    </g>
                                </g>
                                <g transform="translate(-1)"
                                   style="animation:evdw1kaeakn934_to__to 1000ms linear infinite normal forwards">
                                    <ellipse id="evdw1kaeakn934" fill="#2E2F2F" fill-rule="evenodd" stroke="none"
                                             stroke-width="1" rx="2.183" ry="2.182"
                                             transform="translate(24.735 2.182)"/>
                                </g>
                                <rect id="evdw1kaeakn935" width="11.64" height="1.455" fill="#2E2F2F"
                                      fill-rule="evenodd" stroke="none" stroke-width="1" rx=".727" ry="0"/>
                            </g>
                            <path id="evdw1kaeakn936" fill="none" fill-rule="evenodd" stroke="#64676D"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M0 159.2h291"/>
                            <path id="evdw1kaeakn937" fill="#E6E8ED" fill-rule="evenodd" stroke="#64676D"
                                  stroke-width="1.8" d="M105.083 132.19h-25.38l-5.14 25.8h35.659l-5.14-25.8z"/>
                            <rect id="evdw1kaeakn938" width="49.815" height="2.527" fill="#F7F8FA" fill-rule="evenodd"
                                  stroke="#64676D" stroke-width="1.8" rx="1.264" ry="0"
                                  transform="translate(67.485 156.918)"/>
                            <rect id="evdw1kaeakn939" width="145.117" height="107.255" fill="#E6E8ED"
                                  fill-rule="evenodd" stroke="#64676D" stroke-width="1.8" rx="10" ry="0"
                                  transform="translate(19.47 37.645)"/>
                            <g id="evdw1kaeakn940" fill-rule="evenodd" transform="translate(23.28 52.364)">
                                <g id="evdw1kaeakn941" stroke="#FFF" stroke-width="2">
                                    <path id="evdw1kaeakn942" fill="#59BCF9"
                                          d="M17.371 7.362l12.732-.002c5.523-.004 10.001 4.473 10.002 9.995 0 .001 0 .002-.003.004L40.1 30.09c-.004 5.522-4.48 9.997-10.002 10.001l-12.732.002c-5.523.004-10-4.472-10.002-9.995 0 0 0-.002.003-.003l.002-12.732c.004-5.522 4.48-9.998 10.002-10.002z"
                                          transform="rotate(45 23.735 23.727)"/>
                                    <g id="evdw1kaeakn943" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                       transform="translate(12.277 17.182)">
                                        <path id="evdw1kaeakn944" d="M5.794 2.594L.679 6.685l5.115 4.091"/>
                                        <path id="evdw1kaeakn945" d="M22.163 2.594l-5.116 4.091 5.116 4.091"
                                              transform="matrix(-1 0 0 1 39.21 0)"/>
                                        <path id="evdw1kaeakn946" d="M8.594 12.682L14.323.409"/>
                                    </g>
                                </g>
                                <g id="evdw1kaeakn947" transform="translate(91.486 43.545)">
                                    <circle id="evdw1kaeakn948" r="19" fill="#FFF" stroke="none" stroke-width="1"
                                            transform="translate(19 20)"/>
                                    <path id="evdw1kaeakn949" fill="#EF8889" stroke="none" stroke-width="1"
                                          d="M19 3c9.389 0 17 7.611 17 17 0 4.356-1.639 8.33-4.333 11.338l.003.004c-.337.364-.605.642-.803.834-.085.083-.19.181-.315.296l-.073.067-.059.053c-.138.125-.296.266-.475.423.102-.093.207-.183.31-.275l.165-.148.132-.12.146-.137c.337-.319.66-.652.969-.997L7.649 7.348c-.328.304-.596.56-.803.765-.396.406-.774.833-1.13 1.277l24.226 23.621A16.932 16.932 0 0119 37C9.611 37 2 29.389 2 20c0-4.013 1.39-7.702 3.716-10.61l-.005-.005c.415-.495.734-.858.956-1.09l.179-.182A16.951 16.951 0 0119 3z"/>
                                    <g id="evdw1kaeakn950" transform="rotate(45 9.387 25.52)">
                                        <g id="evdw1kaeakn951" fill="#FFF" stroke="none" stroke-width="1"
                                           transform="translate(6.304 4.218)">
                                            <ellipse id="evdw1kaeakn952" rx="3.667" ry="4"
                                                     transform="translate(5.5 4)"/>
                                            <ellipse id="evdw1kaeakn953" rx="5.5" ry="8" transform="translate(5.5 13)"/>
                                        </g>
                                        <path id="evdw1kaeakn954" fill="none" stroke="#FFF" stroke-linecap="round"
                                              stroke-linejoin="round" stroke-width="2"
                                              d="M7.475 2.319c.92.84 1.537 1.524 1.853 2.051.316.527.699 1.51 1.147 2.949"/>
                                        <g id="evdw1kaeakn955" fill="none" stroke="#FFF" stroke-linecap="round"
                                           stroke-linejoin="round" stroke-width="2" transform="translate(.025 7.718)">
                                            <path id="evdw1kaeakn956"
                                                  d="M2.447.528c1.093.902 1.83 1.633 2.214 2.19.383.558.856 1.592 1.42 3.101"
                                                  transform="rotate(-24 4.264 3.174)"/>
                                            <path id="evdw1kaeakn957"
                                                  d="M1.469 5.082c1.072.937 1.798 1.693 2.176 2.27.379.576.85 1.643 1.414 3.2"
                                                  transform="rotate(-47 3.264 7.816)"/>
                                            <path id="evdw1kaeakn958"
                                                  d="M2.615 9.69c1.007.933 1.685 1.691 2.032 2.275.348.584.77 1.672 1.265 3.264"
                                                  transform="rotate(-87 4.264 12.46)"/>
                                        </g>
                                        <g id="evdw1kaeakn959" fill="none" stroke="#FFF" stroke-linecap="round"
                                           stroke-linejoin="round" stroke-width="2"
                                           transform="matrix(-1 0 0 1 22.875 8.425)">
                                            <path id="evdw1kaeakn960"
                                                  d="M2.447.528c1.093.902 1.83 1.633 2.214 2.19.383.558.856 1.592 1.42 3.101"
                                                  transform="rotate(-24 4.264 3.174)"/>
                                            <path id="evdw1kaeakn961"
                                                  d="M1.469 5.082c1.072.937 1.798 1.693 2.176 2.27.379.576.85 1.643 1.414 3.2"
                                                  transform="rotate(-47 3.264 7.816)"/>
                                            <path id="evdw1kaeakn962"
                                                  d="M2.615 9.69c1.007.933 1.685 1.691 2.032 2.275.348.584.77 1.672 1.265 3.264"
                                                  transform="rotate(-87 4.264 12.46)"/>
                                        </g>
                                        <path id="evdw1kaeakn963" fill="none" stroke="#FFF" stroke-linecap="round"
                                              stroke-linejoin="round" stroke-width="2"
                                              d="M16.132.904c-.926.828-1.548 1.506-1.865 2.035-.317.528-.696 1.517-1.135 2.965"/>
                                    </g>
                                    <path id="evdw1kaeakn964" fill="#EF8889" stroke="none" stroke-width="1"
                                          d="M15.884 10.15l2.107-.22.296 24.036-2.431-2.938z"
                                          transform="rotate(-45 17.071 21.948)"/>
                                    <path id="evdw1kaeakn965" fill="#EF8889" stroke="none" stroke-width="1"
                                          d="M18.41 7.144l1.87.088.14 20.99-2.004-.078z"
                                          transform="rotate(-45 19.416 17.682)"/>
                                </g>
                                <path id="evdw1kaeakn966" fill="#FFF" stroke="none" stroke-width="1"
                                      d="M70.386 34.339c2.6 0 4.98.932 6.829 2.48a7.094 7.094 0 003.56 10.456c-1.05 4.769-5.303 8.336-10.39 8.336-5.876 0-10.64-4.762-10.64-10.636s4.764-10.636 10.64-10.636zm-.088-6.885c1.558-1.308 3.41-1.666 4.137-.8.726.865.052 2.626-1.507 3.933-1.558 1.307-3.41 1.665-4.136.8s-.052-2.626 1.506-3.933z"/>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>

            <div class="spinner"></div>

            <h1>We are transferring your store to a new domain now. This may take up to a few minutes.</h1>
            <p>If you have any questions please drop us a line at <a
                        href="mailto:support@x-cart.com">support@x-cart.com</a> or just dial one of the following
                numbers:</p>
            <span class="phones">
                <span class="phones__phone phones__phone--us">
                    <a href="tel:18006577957">1-800-657-7957</a>
                </span>
                <span class="phones__phone phones__phone--uk">
                    <a href="tel:08000488862">0800-048-8862</a>
                </span>
            </span>
        </div>
    </div>
</div>
</body>
</html>

