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

/* /mff/xcart/skins/admin/items_list/model/table/order/cell.payment_actions.twig */
class __TwigTemplate_559054970dfd70ba9f0dbae65be54189dde1330764cbbae5854ae0165e28f779 extends \XLite\Core\Templating\Twig\Template
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
        echo "
";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "hasPaymentActions", [0 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method")) {
            // line 8
            echo "  <div class=\"payment-actions\">
    ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Order\\Details\\Admin\\PaymentActions", "order" => $this->getAttribute(($context["this"] ?? null), "entity", []), "unitsFilter" => $this->getAttribute(($context["this"] ?? null), "getTransactionsFilter", [], "method")]]), "html", null, true);
            echo "
  </div>
";
        }
        // line 12
        echo "
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/items_list/model/table/order/cell.payment_actions.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 12,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/items_list/model/table/order/cell.payment_actions.twig", "");
    }
}
