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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/head/head.title.twig */
class __TwigTemplate_e63475c9e1d46bae099865a9d6bbfa66e90a336c69cfc3387814823aa8277101 extends \XLite\Core\Templating\Twig\Template
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
<td class=\"title-wrapper\">
    <div style=\"display: inline-block; width: 100%\">
        <div class=\"packing-slip-title\">";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPackingSlipTitle", [], "method"), "html", null, true);
        echo " ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getPrintableOrderNumber", [], "method"), "html", null, true);
        echo "</div>
        <div class=\"packing-slip-title-data\">
            <div class=\"packing-slip-title-data-item packing-slip-packing-date\">
                <strong class=\"date-title\">";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Packing date"]), "html", null, true);
        echo "</strong>
                <span class=\"date-value\">";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPackingSlipDateTime", [], "method"), "html", null, true);
        echo "</span>
            </div>
            <div class=\"packing-slip-title-data-item packing-slip-shipping-method\">
                <strong class=\"date-title\">";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Shipping"]), "html", null, true);
        echo "</strong>
                <span class=\"date-value\">";
        // line 17
        echo $this->getAttribute(($context["this"] ?? null), "getShippingMethodName", [], "method");
        echo "</span>
            </div>
            <div class=\"packing-slip-title-data-item packing-slip-order-date\">
                <strong class=\"date-title\">";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Order date"]), "html", null, true);
        echo "</strong>
                <span class=\"date-value\">";
        // line 21
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatTime", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getDate", [], "method")], "method"), "html", null, true);
        echo "</span>
            </div>
            ";
        // line 23
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getPaymentTransactionData", [0 => 1], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 24
            echo "              <div class=\"packing-slip-title-data-item payment-sub-data\">
                  <strong class=\"date-title\">";
            // line 25
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute($context["item"], "title", [])]), "html", null, true);
            echo "</strong>
                  <span class=\"date-value\">";
            // line 26
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "value", []), "html", null, true);
            echo "</span>
              </div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "        </div>
    </div>
</td>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/head/head.title.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 29,  83 => 26,  79 => 25,  76 => 24,  72 => 23,  67 => 21,  63 => 20,  57 => 17,  53 => 16,  47 => 13,  43 => 12,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/head/head.title.twig", "");
    }
}
