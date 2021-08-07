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

/* modules/XC/ProductComparison/comparison_table/parts/attributes/list.twig */
class __TwigTemplate_8b15d4736eacb79855661c05a4134ab0ed9e21f3391f69729f6415ec53762f29 extends \XLite\Core\Templating\Twig\Template
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
        // line 4
        echo "
";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "getAttributeGroup", [], "method")) {
            // line 6
            echo "<tr class=\"group\">
  <td class=\"title\"><span>";
            // line 7
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method"), "html", null, true);
            echo "</span></td>
  ";
            // line 8
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 9
                echo "    <td>&nbsp;</td>
  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            echo "<tr>
";
        }
        // line 13
        echo "
";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getAttributesList", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["a"]) {
            // line 15
            echo "  <tr>
    <td";
            // line 16
            if ($this->getAttribute(($context["this"] ?? null), "getAttributeGroup", [], "method")) {
                echo " class=\"indented\"";
            }
            echo "><span>";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["a"], "getName", [], "method"), "html", null, true);
            echo "</span></td>
    ";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 18
                echo "      <td>";
                echo $this->getAttribute(($context["this"] ?? null), "getAttributeValue", [0 => $context["a"], 1 => $context["product"]], "method");
                echo "</td>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            echo "  </tr>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['a'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/comparison_table/parts/attributes/list.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 20,  79 => 18,  75 => 17,  67 => 16,  64 => 15,  60 => 14,  57 => 13,  53 => 11,  46 => 9,  42 => 8,  38 => 7,  35 => 6,  33 => 5,  30 => 4,);
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
 # Attribute list
 #}

{% if this.getAttributeGroup() %}
<tr class=\"group\">
  <td class=\"title\"><span>{{ this.getTitle() }}</span></td>
  {% for product in this.getProducts() %}
    <td>&nbsp;</td>
  {% endfor %}
<tr>
{% endif %}

{% for a in this.getAttributesList() %}
  <tr>
    <td{% if this.getAttributeGroup() %} class=\"indented\"{% endif %}><span>{{ a.getName() }}</span></td>
    {% for product in this.getProducts() %}
      <td>{{ this.getAttributeValue(a, product)|raw }}</td>
    {% endfor %}
  </tr>
{% endfor %}
", "modules/XC/ProductComparison/comparison_table/parts/attributes/list.twig", "/mff/xcart/skins/customer/modules/XC/ProductComparison/comparison_table/parts/attributes/list.twig");
    }
}
