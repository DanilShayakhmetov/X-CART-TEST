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

/* modules/XC/ProductComparison/comparison_table/parts/product_name.twig */
class __TwigTemplate_fdb7f720b0073076ecb19823d2ab1596948dc64ba9c5c7b968fdf8d7173ae515 extends \XLite\Core\Templating\Twig\Template
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
<tr class=\"names\">
  <td><div>&nbsp;</div></td>
  ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 10
            echo "    <td>
      <div>
        <a target=\"_blank\" href=\"";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "product", "", ["product_id" => $this->getAttribute($context["product"], "product_id", [])]]), "html", null, true);
            echo "\"><span>";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["product"], "name", []), "html", null, true);
            echo "</span></a>
        <img src=\"";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
            echo "\" class=\"right-fade\" alt=\"\" />
      </div>
    </td>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "</tr>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/comparison_table/parts/product_name.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 17,  49 => 13,  43 => 12,  39 => 10,  35 => 9,  30 => 6,);
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
 # Products
 #
 # @ListChild (list=\"comparison_table.header_fixed\", weight=\"100\")
 #}

<tr class=\"names\">
  <td><div>&nbsp;</div></td>
  {% for product in this.getProducts() %}
    <td>
      <div>
        <a target=\"_blank\" href=\"{{ url('product', '', {'product_id': product.product_id}) }}\"><span>{{ product.name }}</span></a>
        <img src=\"{{ asset('images/spacer.gif') }}\" class=\"right-fade\" alt=\"\" />
      </div>
    </td>
  {% endfor %}
</tr>
", "modules/XC/ProductComparison/comparison_table/parts/product_name.twig", "/mff/xcart/skins/customer/modules/XC/ProductComparison/comparison_table/parts/product_name.twig");
    }
}
