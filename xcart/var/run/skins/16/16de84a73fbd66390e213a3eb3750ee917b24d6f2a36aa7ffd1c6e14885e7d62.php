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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/addresses/addresses.purchaser.twig */
class __TwigTemplate_7d09cf23f66f8919a3e207657628daba5572544603ff28a3e6b1f84543de4406 extends \XLite\Core\Templating\Twig\Template
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
        echo "<td class=\"address-data\">
  <ul cellspacing=\"0\" class=\"addresses\">

    ";
        // line 9
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isShippingSectionVisible", [], "method")) {
            // line 10
            echo "      <li class=\"address shipping\">
        <div class=\"wrapper";
            // line 11
            if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "trackingNumbers", []), "isEmpty", [], "method")) {
                echo " tracking-info-section-included";
            }
            echo "\">
          ";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "packing_slip.addresses.shipping", "baddress" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "profile", []), "billing_address", []), "saddress" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "profile", []), "shipping_address", [])]]), "html", null, true);
            echo "
        </div>
      </li>
    ";
        }
        // line 16
        echo "
    ";
        // line 17
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isPaymentSectionVisible", [], "method")) {
            // line 18
            echo "      <li class=\"address payment";
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isShippingSectionVisible", [], "method")) {
                echo " payment-wrapping";
            }
            echo "\">
        <div class=\"wrapper\">
          ";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "packing_slip.addresses.billing", "baddress" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "profile", []), "billing_address", []), "saddress" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "profile", []), "shipping_address", [])]]), "html", null, true);
            echo "
        </div>
      </li>
    ";
        }
        // line 24
        echo "
  </ul>
</td>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/addresses/addresses.purchaser.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 24,  66 => 20,  58 => 18,  56 => 17,  53 => 16,  46 => 12,  40 => 11,  37 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/addresses/addresses.purchaser.twig", "");
    }
}
