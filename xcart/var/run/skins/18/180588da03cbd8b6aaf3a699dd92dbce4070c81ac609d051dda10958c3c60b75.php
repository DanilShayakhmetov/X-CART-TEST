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

/* /home/ruslan/Projects/next/output/xcart/src/skins/mail/common/modules/XPay/XPaymentsCloud/order/invoice/parts/item.subscription.twig */
class __TwigTemplate_e6c048743e63312153c1c3f507e6a7acd1a663347313946d7ebb60ada816b41b extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "isXpaymentsSubscription", [], "method")) {
            // line 8
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "isInitialXpaymentsSubscription", [], "method")) {
                // line 9
                echo "  <li class=\"subscription-setup-fee\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Setup fee"]), "html", null, true);
                echo ": ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getDisplayPrice", [], "method") - $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getXpaymentsDisplayFeePrice", [], "method"))], "method"), "html", null, true);
                echo "</li>
";
            }
            // line 11
            echo "<li class=\"subscription-fee\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Subscription fee"]), "html", null, true);
            echo ": ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getXpaymentsDisplayFeePrice", [], "method")], "method"), "html", null, true);
            echo "</li>
";
            // line 12
            if ($this->getAttribute(($context["this"] ?? null), "isXpaymentsNextPaymentDateAvailable", [0 => $this->getAttribute(($context["this"] ?? null), "item", [])], "method")) {
                // line 13
                echo "<li class=\"subscription-planned-date\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Planned date of the next payment"]), "html", null, true);
                echo ": ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatDate", [0 => $this->getAttribute(($context["this"] ?? null), "getNextPaymentDate", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "xpaymentsSubscription", [])], "method")], "method"), "html", null, true);
                echo "</li>
";
                // line 14
                if ($this->getAttribute(($context["this"] ?? null), "isXpaymentsLastPaymentFailed", [0 => $this->getAttribute(($context["this"] ?? null), "item", [])], "method")) {
                    // line 15
                    echo "  <li class=\"subscription-actual-date\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Next attempt date"]), "html", null, true);
                    echo ": ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatDate", [0 => $this->getAttribute(($context["this"] ?? null), "getNextAttemptDate", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "xpaymentsSubscription", [])], "method")], "method"), "html", null, true);
                    echo "</li>
";
                }
            }
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/mail/common/modules/XPay/XPaymentsCloud/order/invoice/parts/item.subscription.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  60 => 15,  58 => 14,  51 => 13,  49 => 12,  42 => 11,  34 => 9,  32 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/mail/common/modules/XPay/XPaymentsCloud/order/invoice/parts/item.subscription.twig", "");
    }
}
