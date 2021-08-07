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

/* modules/CDev/SimpleCMS/footer_menu.twig */
class __TwigTemplate_9b54050d5027fe11c4549d60a8a9e39c34017d0b9f686d8a9e0a26eda6cabcdd extends \XLite\Core\Templating\Twig\Template
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
<div id=\"secondary-menu\" class=\"clearfix\">
 <ul class=\"footer-menu\">
    ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getItems", [], "method"));
        foreach ($context['_seq'] as $context["i"] => $context["item"]) {
            // line 8
            echo "      ";
            if ($this->getAttribute(($context["this"] ?? null), "isLevelUp", [0 => $this->getAttribute($context["item"], "depth", [])], "method")) {
                // line 9
                echo "        <ul>
      ";
            } else {
                // line 11
                echo "        ";
                if ( !$this->getAttribute(($context["this"] ?? null), "isFirstElement", [], "method")) {
                    // line 12
                    echo "        </li>
        ";
                }
                // line 14
                echo "      ";
            }
            // line 15
            echo "
      ";
            // line 16
            echo $this->getAttribute(($context["this"] ?? null), "closeMenuList", [0 => $this->getAttribute($context["item"], "depth", [])], "method");
            echo "
      <li ";
            // line 17
            echo $this->getAttribute(($context["this"] ?? null), "displayItemClass", [0 => $context["i"], 1 => $context["item"]], "method");
            echo ">
        ";
            // line 18
            if ($this->getAttribute($context["item"], "url", [])) {
                // line 19
                echo "        <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "url", []), "html", null, true);
                echo "\" ";
                if ($this->getAttribute($context["item"], "active", [])) {
                    echo "class=\"active\"";
                }
                echo ">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "label", []), "html", null, true);
                echo "</a>
        ";
            } else {
                // line 21
                echo "        <span class=\"footer-title\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "label", []), "html", null, true);
                echo "</span>
        ";
            }
            // line 23
            echo "          
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['i'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 25
        echo "      </li>
    ";
        // line 26
        echo $this->getAttribute(($context["this"] ?? null), "closeMenuList", [], "method");
        echo "
 </ul>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/CDev/SimpleCMS/footer_menu.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  97 => 26,  94 => 25,  87 => 23,  81 => 21,  69 => 19,  67 => 18,  63 => 17,  59 => 16,  56 => 15,  53 => 14,  49 => 12,  46 => 11,  42 => 9,  39 => 8,  35 => 7,  30 => 4,);
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
 # Top menu
 #}

<div id=\"secondary-menu\" class=\"clearfix\">
 <ul class=\"footer-menu\">
    {% for i, item in this.getItems() %}
      {% if this.isLevelUp(item.depth) %}
        <ul>
      {% else %}
        {% if not this.isFirstElement() %}
        </li>
        {% endif %}
      {% endif %}

      {{ this.closeMenuList(item.depth)|raw }}
      <li {{ this.displayItemClass(i, item)|raw }}>
        {% if item.url %}
        <a href=\"{{ item.url }}\" {% if item.active %}class=\"active\"{% endif %}>{{ item.label }}</a>
        {% else %}
        <span class=\"footer-title\">{{ item.label }}</span>
        {% endif %}
          
    {% endfor %}
      </li>
    {{ this.closeMenuList()|raw }}
 </ul>
</div>", "modules/CDev/SimpleCMS/footer_menu.twig", "/mff/xcart/skins/customer/modules/CDev/SimpleCMS/footer_menu.twig");
    }
}
