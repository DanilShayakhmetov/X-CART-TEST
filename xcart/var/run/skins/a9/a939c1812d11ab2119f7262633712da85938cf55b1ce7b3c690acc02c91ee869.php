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

/* /mff/xcart/skins/admin/modules/XPay/XPaymentsCloud/order/order/payment_actions/charge_difference.twig */
class __TwigTemplate_005101aa7c3a45ceac5b4b34803c3277f6e566f72061267cd5f7e4d9aea3f879 extends \XLite\Core\Templating\Twig\Template
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
        // line 7
        echo "
";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "isXpaymentsChargeDifferenceAvailable", [], "method")) {
            // line 9
            echo "  <div class=\"rebill-button-container\">

    <button type=\"button\" class=\"btn regular-button create-inline\" onclick=\"javascript: ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getXpaymentsChargeDifferenceJsCode", [], "method"), "html", null, true);
            echo ";\">
      ";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Charge the difference"]), "html", null, true);
            echo "
    </button>

  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/XPay/XPaymentsCloud/order/order/payment_actions/charge_difference.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 12,  39 => 11,  35 => 9,  33 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/XPay/XPaymentsCloud/order/order/payment_actions/charge_difference.twig", "");
    }
}
