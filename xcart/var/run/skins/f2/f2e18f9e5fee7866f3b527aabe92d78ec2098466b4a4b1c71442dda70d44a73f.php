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

/* items_list/product/parts/common.product-price.twig */
class __TwigTemplate_2b5d62f1f7f26afdcb771ccec33af6f106d825804ca59ca3022a44e5bb65afbc extends \XLite\Core\Templating\Twig\Template
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
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Price", "product" => $this->getAttribute(($context["this"] ?? null), "product", []), "displayOnlyPrice" => "1", "allowRange" => "1"]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "items_list/product/parts/common.product-price.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 10,);
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
 # Item price
 #
 # @ListChild (list=\"itemsList.product.grid.customer.info\", weight=\"30\")
 # @ListChild (list=\"itemsList.product.small_thumbnails.customer.details\", weight=\"25\")
 # @ListChild (list=\"itemsList.product.list.customer.info\", weight=\"40\")
 # @ListChild (list=\"itemsList.product.table.customer.columns\", weight=\"40\")
 # @ListChild (list=\"productBlock.info\", weight=\"300\")
 #}
{{ widget('\\\\XLite\\\\View\\\\Price', product=this.product, displayOnlyPrice='1', allowRange='1') }}", "items_list/product/parts/common.product-price.twig", "/mff/xcart/skins/customer/items_list/product/parts/common.product-price.twig");
    }
}
