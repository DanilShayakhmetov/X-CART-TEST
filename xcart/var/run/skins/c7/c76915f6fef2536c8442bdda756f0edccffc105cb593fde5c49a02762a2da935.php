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

/* /mff/xcart/skins/customer/items_list/product/parts/common.button-add2cart.twig */
class __TwigTemplate_8b2f5d9f65f094d7b5f18ebf80f0a501f41581a42f126ea4e7363cb19f8446de extends \XLite\Core\Templating\Twig\Template
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
        if (($this->getAttribute(($context["this"] ?? null), "isShowAdd2CartBlock", [], "method") && $this->getAttribute(($context["this"] ?? null), "getAdd2CartBlockWidget", [], "method"))) {
            // line 8
            echo "    ";
            echo $this->getAttribute($this->getAttribute(($context["this"] ?? null), "getAdd2CartBlockWidget", [], "method"), "display", [], "method");
            echo "
";
        }
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "itemsList.product.table.customer.add2cart"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/items_list/product/parts/common.button-add2cart.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/items_list/product/parts/common.button-add2cart.twig", "");
    }
}
