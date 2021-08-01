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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/address.state.twig */
class __TwigTemplate_eec277c39c8a8d5db3ae2bbe8a1c9da09a8c5e3b051340790be16f3bd0a95694 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isStateFieldVisible", [], "method")) {
            // line 8
            echo "  <li class=\"state\">
    ";
            // line 9
            if ($this->getAttribute(($context["this"] ?? null), "hasField", [0 => "country_code"], "method")) {
                // line 10
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Select\\State", "fieldName" => "destination_state", "value" => $this->getAttribute(($context["this"] ?? null), "getState", [], "method"), "style" => "field-required", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["State"]), "required" => "true"]]), "html", null, true);
                echo "
    ";
            }
            // line 12
            echo "    ";
            if ( !$this->getAttribute(($context["this"] ?? null), "hasField", [0 => "country_code"], "method")) {
                // line 13
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Select\\State", "fieldName" => "destination_state", "value" => $this->getAttribute(($context["this"] ?? null), "getState", [], "method"), "style" => "field-required", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["State"]), "required" => "true", "country" => $this->getAttribute(($context["this"] ?? null), "getCountryCode", [], "method")]]), "html", null, true);
                echo "
    ";
            }
            // line 15
            echo "  </li>
";
        }
        // line 17
        echo "
";
        // line 18
        if ($this->getAttribute(($context["this"] ?? null), "isCustomStateFieldVisible", [], "method")) {
            // line 19
            echo "  <li class=\"state\">
    ";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\Text", "fieldName" => "destination_custom_state", "value" => $this->getAttribute(($context["this"] ?? null), "getOtherState", [], "method"), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["State"])]]), "html", null, true);
            echo "
  </li>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/address.state.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  67 => 20,  64 => 19,  62 => 18,  59 => 17,  55 => 15,  49 => 13,  46 => 12,  40 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/address.state.twig", "");
    }
}
