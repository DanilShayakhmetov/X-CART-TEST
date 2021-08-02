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

/* modules/XPay/XPaymentsCloud/order/fraud_status/status.twig */
class __TwigTemplate_07435cb1d14f57da48e5fcd1a486f3a0ac729b7674e16ba69be008eb0b4e36ff extends \XLite\Core\Templating\Twig\Template
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
<a href=\"";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getXpaymentsFraudInfoLink", [0 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method"), "html", null, true);
        echo "\" title=\"";
        echo $this->getAttribute(($context["this"] ?? null), "getXpaymentsFraudInfoTitle", [0 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method");
        echo "\"></a>
";
    }

    public function getTemplateName()
    {
        return "modules/XPay/XPaymentsCloud/order/fraud_status/status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 6,  30 => 5,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XPay/XPaymentsCloud/order/fraud_status/status.twig", "/mff/xcart/skins/admin/modules/XPay/XPaymentsCloud/order/fraud_status/status.twig");
    }
}
