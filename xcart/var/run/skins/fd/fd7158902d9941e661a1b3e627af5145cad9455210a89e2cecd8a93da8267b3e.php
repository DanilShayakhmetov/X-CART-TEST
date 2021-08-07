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

/* /mff/xcart/skins/admin/modules/QSL/BraintreeVZ/config/status.twig */
class __TwigTemplate_54e3744531f70786a87895083e226cf9c447e6b13d4a872d4b3888457f4b7c26 extends \XLite\Core\Templating\Twig\Template
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
        // line 5
        echo "
";
        // line 6
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "paymentMethod", []), "getSetting", [0 => "merchantId"], "method")) {
            // line 7
            echo "  <strong>";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Merchant ID"]), "html", null, true);
            echo ":</strong> <span id=\"braintree-merchant-id\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "paymentMethod", []), "getSetting", [0 => "merchantId"], "method"), "html", null, true);
            echo "</span>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/QSL/BraintreeVZ/config/status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 7,  33 => 6,  30 => 5,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/QSL/BraintreeVZ/config/status.twig", "");
    }
}
