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

/* /mff/xcart/skins/customer/modules/XC/FastLaneCheckout/blocks/popup_address_form/apply_to_billing.twig */
class __TwigTemplate_bd6af899842b853c34aa4473e3c06f8a2f354daa66e1086fa16649955aa1326b extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isSameAddressVisible", [], "method")) {
            // line 8
            echo "    <li class=\"item-same_address input-checkbox-enabled clearfix form-group\">
        ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Checkbox\\ApplyToBilling", "value" => $this->getAttribute(($context["this"] ?? null), "isSameAddress", [], "method"), "fieldName" => "same_address", "fieldId" => "popup_same_address"]]), "html", null, true);
            echo "
    </li>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XC/FastLaneCheckout/blocks/popup_address_form/apply_to_billing.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/XC/FastLaneCheckout/blocks/popup_address_form/apply_to_billing.twig", "");
    }
}
