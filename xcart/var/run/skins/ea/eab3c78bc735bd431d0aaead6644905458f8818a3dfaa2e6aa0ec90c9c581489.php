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

/* product/details/stock/label.twig */
class __TwigTemplate_85f7a0cbe5c6a661ece85026bdefb4b6f20604fef5b4df5b7758728fb108ca52 extends \XLite\Core\Templating\Twig\Template
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
        echo "
";
        // line 8
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "isShowStockWarning", [], "method")) {
            // line 9
            echo "  <span class=\"product-items-available low-stock\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Only X left in stock", ["X" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getAvailableAmount", [], "method")]]), "html", null, true);
            echo "</span>
";
        }
    }

    public function getTemplateName()
    {
        return "product/details/stock/label.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 9,  33 => 8,  30 => 7,);
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
 # Product low stock label
 #
 # @ListChild (list=\"itemsList.product.grid.customer.info\", weight=\"31\")
 # @ListChild (list=\"itemsList.product.list.customer.info\", weight=\"2000\")
 #}

{% if this.product.isShowStockWarning() %}
  <span class=\"product-items-available low-stock\">{{ t('Only X left in stock', {'X': this.product.getAvailableAmount()}) }}</span>
{% endif %}
", "product/details/stock/label.twig", "/mff/xcart/skins/customer/product/details/stock/label.twig");
    }
}
