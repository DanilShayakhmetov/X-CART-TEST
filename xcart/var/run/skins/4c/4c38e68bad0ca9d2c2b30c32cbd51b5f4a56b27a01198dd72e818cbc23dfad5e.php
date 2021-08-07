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

/* left_menu/body.twig */
class __TwigTemplate_ba82bff49c96fb124e7dc351b22ac17b707d552c6f2bc62740c9d290fba25df4 extends \XLite\Core\Templating\Twig\Template
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
<div ";
        // line 5
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getContainerTagAttributes", [], "method")], "method");
        echo ">
  ";
        // line 6
        $context["items"] = $this->getAttribute(($context["this"] ?? null), "getItems", [], "method");
        // line 7
        echo "  ";
        if (($context["items"] ?? null)) {
            // line 8
            echo "    <ul class=\"menu main-menu\">
      ";
            // line 9
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 10
                echo "        ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "display", [], "method"), "html", null, true);
                echo "
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 12
            echo "    </ul>
  ";
        }
        // line 14
        echo "
  ";
        // line 15
        $context["bottomItems"] = $this->getAttribute(($context["this"] ?? null), "getBottomItems", [], "method");
        // line 16
        echo "  ";
        if (($context["bottomItems"] ?? null)) {
            // line 17
            echo "    <ul class=\"menu bottom-menu\">
      ";
            // line 18
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["bottomItems"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
                // line 19
                echo "        ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["link"], "display", [], "method"), "html", null, true);
                echo "
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "    </ul>
  ";
        }
        // line 23
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "left_menu/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 23,  86 => 21,  77 => 19,  73 => 18,  70 => 17,  67 => 16,  65 => 15,  62 => 14,  58 => 12,  49 => 10,  45 => 9,  42 => 8,  39 => 7,  37 => 6,  33 => 5,  30 => 4,);
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
 # Left side menu
 #}

<div {{ this.printTagAttributes(this.getContainerTagAttributes())|raw }}>
  {% set items = this.getItems() %}
  {% if items %}
    <ul class=\"menu main-menu\">
      {% for item in items %}
        {{ item.display() }}
      {% endfor %}
    </ul>
  {% endif %}

  {% set bottomItems = this.getBottomItems() %}
  {% if bottomItems %}
    <ul class=\"menu bottom-menu\">
      {% for link in bottomItems %}
        {{ link.display() }}
      {% endfor %}
    </ul>
  {% endif %}
</div>
", "left_menu/body.twig", "/mff/xcart/skins/admin/left_menu/body.twig");
    }
}
