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

/* mini_cart/horizontal/parts/cart.twig */
class __TwigTemplate_e4b32b73ac6b3e14404d55574f7f41d730a514228dc33b21ee5ee843e1e4d2cf extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\SimpleLink", "label" => "View cart", "location" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "cart"]), "style" => "regular-button cart"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "mini_cart/horizontal/parts/cart.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{##
 # Horizontal minicart cart button block
 #
 # @ListChild (list=\"minicart.horizontal.buttons\", weight=\"5\")
 #}
{{ widget('\\\\XLite\\\\View\\\\Button\\\\SimpleLink', label='View cart', location=url('cart'), style='regular-button cart') }}
", "mini_cart/horizontal/parts/cart.twig", "/mff/xcart/skins/customer/mini_cart/horizontal/parts/cart.twig");
    }
}
