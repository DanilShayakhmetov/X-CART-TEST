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

/* /mff/xcart/skins/admin/order/page/parts/line1.twig */
class __TwigTemplate_5fd9198f4aef586159e8ed8728affe17beb665d0d185a06d8a386fdd65aba3ca extends \XLite\Core\Templating\Twig\Template
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

<div class=\"line-1 clearfix\">
    <div class=\"payment-and-shipping\">
        <div class=\"clearfix\">
            ";
        // line 11
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isPaymentSectionVisible", [], "method")) {
            // line 12
            echo "                <div class=\"order-part payment\">
                    <h4>";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Payment method"]), "html", null, true);
            echo "</h4>
                    <div class=\"box\">";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "order.payment.method"]]), "html", null, true);
            echo "</div>
                </div>
            ";
        }
        // line 17
        echo "
            ";
        // line 18
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isShippingSectionVisible", [], "method")) {
            // line 19
            echo "                <div class=\"order-part shipping\">
                    <h4>";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Shipping method"]), "html", null, true);
            echo "</h4>
                    <div class=\"box\">";
            // line 21
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "order.shipping.method"]]), "html", null, true);
            echo "</div>
                </div>
            ";
        }
        // line 24
        echo "        </div>

        <div class=\"clearfix\">
            ";
        // line 27
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isShippingSectionVisible", [], "method")) {
            // line 28
            echo "                <div class=\"order-part shipping\">
                    <div class=\"box\">";
            // line 29
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "order.shipping.address"]]), "html", null, true);
            echo "</div>
                </div>
            ";
        }
        // line 32
        echo "
          ";
        // line 33
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isPaymentSectionVisible", [], "method")) {
            // line 34
            echo "            <div class=\"order-part payment\">
              <div class=\"box\">";
            // line 35
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "order.payment.address"]]), "html", null, true);
            echo "</div>
            </div>
          ";
        }
        // line 38
        echo "        </div>
    </div>

    <div class=\"actions\">
        ";
        // line 42
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "order.actions"]]), "html", null, true);
        echo "
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/order/page/parts/line1.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  106 => 42,  100 => 38,  94 => 35,  91 => 34,  89 => 33,  86 => 32,  80 => 29,  77 => 28,  75 => 27,  70 => 24,  64 => 21,  60 => 20,  57 => 19,  55 => 18,  52 => 17,  46 => 14,  42 => 13,  39 => 12,  37 => 11,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/order/page/parts/line1.twig", "");
    }
}
