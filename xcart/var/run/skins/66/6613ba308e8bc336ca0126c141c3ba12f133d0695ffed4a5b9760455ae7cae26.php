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

/* modules/XPay/XPaymentsCloud/shopping_cart/parts/item.info.subscription.twig */
class __TwigTemplate_dfd47bb44986079f186e1fa7ee443796d993baa8517dfc658af6cd4cf04f6dfb extends \XLite\Core\Templating\Twig\Template
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
            echo "  <p class=\"item-subscription\">
    ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "cart.item.info.xpayments_subscription", "xpaymentsSubscription" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "product", []), "xpaymentsSubscriptionPlan", []), "orderItem" => $this->getAttribute(($context["this"] ?? null), "item", [])]]), "html", null, true);
            echo "
  </p>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/XPay/XPaymentsCloud/shopping_cart/parts/item.info.subscription.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 9,  32 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XPay/XPaymentsCloud/shopping_cart/parts/item.info.subscription.twig", "/mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/shopping_cart/parts/item.info.subscription.twig");
    }
}
