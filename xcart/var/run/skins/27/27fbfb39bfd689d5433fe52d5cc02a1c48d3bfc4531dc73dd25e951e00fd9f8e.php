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

/* items_list/product/parts/common.labels.twig */
class __TwigTemplate_379c441d11d16a1f47e4bb7ca10b4dbfda431d73b05ea1aa53f600db7c11b48c extends \XLite\Core\Templating\Twig\Template
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
        // line 12
        echo "
";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Labels", "labels" => $this->getAttribute(($context["this"] ?? null), "getLabels", [], "method")]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "items_list/product/parts/common.labels.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 13,  30 => 12,);
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
 # Labels list
 #
 # @ListChild (list=\"itemsList.product.grid.customer.info\", weight=\"998\")
 # @ListChild (list=\"itemsList.product.small_thumbnails.customer.details\", weight=\"30\")
 # @ListChild (list=\"itemsList.product.list.customer.photo\", weight=\"998\")
 # @ListChild (list=\"itemsList.product.table.customer.columns\", weight=\"25\")
 # @ListChild (list=\"itemsList.product.big_thumbnails.customer.info\", weight=\"998\")
 # @ListChild (list=\"productBlock.info.photo\", weight=\"998\")
 # @ListChild (list=\"product.details.page.image\", weight=\"17\")
 #}

{{ widget('\\\\XLite\\\\View\\\\Labels', labels=this.getLabels()) }}
", "items_list/product/parts/common.labels.twig", "/mff/xcart/skins/customer/items_list/product/parts/common.labels.twig");
    }
}
