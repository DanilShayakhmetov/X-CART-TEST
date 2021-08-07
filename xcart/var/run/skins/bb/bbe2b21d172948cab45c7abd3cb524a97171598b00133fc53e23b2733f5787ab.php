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

/* modules/XC/ProductComparison/comparison_table/parts/prices.twig */
class __TwigTemplate_2626201d27961e1741894f7051ffcdab4b7b57918be22a466eff49e595c14e7e extends \XLite\Core\Templating\Twig\Template
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
<tr class=\"prices\">
  <td class=\"title\">";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Price"]), "html", null, true);
        echo "</td>
  ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 10
            echo "    <td>";
            echo $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => $this->getAttribute($context["product"], "getDisplayPrice", [], "method"), 1 => $this->getAttribute(($context["this"] ?? null), "null", []), 2 => 1], "method");
            echo "</td>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 12
        echo "</tr>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/comparison_table/parts/prices.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 12,  42 => 10,  38 => 9,  34 => 8,  30 => 6,);
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
 # Prices
 #
 # @ListChild (list=\"comparison_table.data\", weight=\"100\")
 #}

<tr class=\"prices\">
  <td class=\"title\">{{ t('Price') }}</td>
  {% for product in this.getProducts() %}
    <td>{{ this.formatPrice(product.getDisplayPrice(), this.null, 1)|raw }}</td>
  {% endfor %}
</tr>
", "modules/XC/ProductComparison/comparison_table/parts/prices.twig", "/mff/xcart/skins/customer/modules/XC/ProductComparison/comparison_table/parts/prices.twig");
    }
}