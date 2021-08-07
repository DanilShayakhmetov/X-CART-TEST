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

/* modules/Kliken/GoogleAds/verification-token.twig */
class __TwigTemplate_5b46168e5dda9978ce2f9a1c0cfb7bafb09fd551d1e4b64e5535b928b562639d extends \XLite\Core\Templating\Twig\Template
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
        // line 4
        echo "
";
        // line 5
        $context["googleToken"] = $this->getAttribute(($context["this"] ?? null), "getGoogleVerificationToken", [], "method");
        // line 6
        if (($context["googleToken"] ?? null)) {
            // line 7
            echo "    <meta name=\"google-site-verification\" content=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["googleToken"] ?? null), "html", null, true);
            echo "\" />
";
        }
    }

    public function getTemplateName()
    {
        return "modules/Kliken/GoogleAds/verification-token.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{##
 # Stuff to put in header
 #}

{% set googleToken = this.getGoogleVerificationToken() %}
{% if googleToken %}
    <meta name=\"google-site-verification\" content=\"{{ googleToken }}\" />
{% endif %}
", "modules/Kliken/GoogleAds/verification-token.twig", "/mff/xcart/skins/customer/modules/Kliken/GoogleAds/verification-token.twig");
    }
}
