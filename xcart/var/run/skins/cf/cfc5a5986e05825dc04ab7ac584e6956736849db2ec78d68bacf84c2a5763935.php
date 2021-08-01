<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/Amazon/PayWithAmazon/header/parts/amazon_config.twig */
class __TwigTemplate_d28603af9a50490b634e2aa95ebbf12664054d51f2294c70e86804b43f807e2e extends \XLite\Core\Templating\Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "isAmazonConfigured", [], "method")) {
            // line 7
            echo "  ";
            $context["amazonConfig"] = $this->getAttribute(($context["this"] ?? null), "getAmazonConfig", [], "method");
            // line 8
            echo "  <script>
    var amazonConfig = {
      sid: '";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["amazonConfig"] ?? null), "merchant_id", []), "html", null, true);
            echo "',
      mode: '";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["amazonConfig"] ?? null), "mode", []), "html", null, true);
            echo "',
      clientId: '";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["amazonConfig"] ?? null), "client_id", []), "html", null, true);
            echo "'
    };

    window.onAmazonLoginReady = function() {
      amazon.Login.setSandboxMode(";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "isSandboxMode", [], "method"), "html", null, true);
            echo ");
      amazon.Login.setClientId(amazonConfig.clientId);
      amazon.Login.setRegion('";
            // line 18
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["amazonConfig"] ?? null), "region", []), "html", null, true);
            echo "');
      amazon.Login.setUseCookie(true);

      ";
            // line 21
            if (( !$this->getAttribute(($context["this"] ?? null), "isLogged", [], "method") || (($this->getAttribute(($context["this"] ?? null), "getTarget", [], "method") == "cart") && $this->getAttribute(($context["this"] ?? null), "isAmazonReturn", [], "method")))) {
                // line 22
                echo "      if (xliteConfig.target !== 'amazon_checkout') {
        amazon.Login.logout();
      }
      ";
            }
            // line 26
            echo "    };

    window.onAmazonPaymentsReady = function () {
      define('Amazon/Config', function () {
        return amazonConfig;
      });
    }
  </script>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/Amazon/PayWithAmazon/header/parts/amazon_config.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 26,  67 => 22,  65 => 21,  59 => 18,  54 => 16,  47 => 12,  43 => 11,  39 => 10,  35 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/Amazon/PayWithAmazon/header/parts/amazon_config.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/Amazon/PayWithAmazon/header/parts/amazon_config.twig");
    }
}
