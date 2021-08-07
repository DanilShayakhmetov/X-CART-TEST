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

/* /mff/xcart/skins/crisp_white/customer/modules/Amazon/PayWithAmazon/checkout_button/add2cart_popup.twig */
class __TwigTemplate_6ab8b09aaddd4803a664be9c6eb621ddff1380a306df2f3930b63d18d59f6457 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isPayWithAmazonActive", [], "method")) {
            // line 7
            echo "  <div id=\"payWithAmazonDiv_add2c_popup_btn\" class=\"pay-with-amazon-button add2cart-popup-pay-with-amazon\"></div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/crisp_white/customer/modules/Amazon/PayWithAmazon/checkout_button/add2cart_popup.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/crisp_white/customer/modules/Amazon/PayWithAmazon/checkout_button/add2cart_popup.twig", "");
    }
}
