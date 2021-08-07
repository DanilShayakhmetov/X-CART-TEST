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

/* menu/account/link.twig */
class __TwigTemplate_05b2df8d1c0f0e8049a022ac85075d979d2649084420d1777f7d2688d1b54a51 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"line\">
  ";
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "getLink", [], "method")) {
            // line 7
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLink", [], "method"), "html", null, true);
            echo "\" class=\"link\"";
            if ($this->getAttribute(($context["this"] ?? null), "getTooltip", [], "method")) {
                echo " title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTooltip", [], "method"), "html", null, true);
                echo "\"";
            }
            if ($this->getAttribute(($context["this"] ?? null), "getBlankPage", [], "method")) {
                echo " target=\"_blank\"";
            }
            echo ">";
            echo $this->getAttribute(($context["this"] ?? null), "getIcon", [], "method");
            if ($this->getAttribute(($context["this"] ?? null), "getTitle", [], "method")) {
                echo "<span>";
                echo $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method");
                echo "</span>";
            }
            echo "</a>
  ";
        } else {
            // line 9
            echo "    <span class=\"link\"";
            if ($this->getAttribute(($context["this"] ?? null), "getTooltip", [], "method")) {
                echo " title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTooltip", [], "method"), "html", null, true);
                echo "\"";
            }
            echo ">";
            echo $this->getAttribute(($context["this"] ?? null), "getIcon", [], "method");
            if ($this->getAttribute(($context["this"] ?? null), "getTitle", [], "method")) {
                echo "<span>";
                echo $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method");
                echo "</span>";
            }
            echo "</span>
  ";
        }
        // line 11
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getLabel", [], "method")) {
            // line 12
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLabelLink", [], "method"), "html", null, true);
            echo "\" class=\"label\"><span title=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLabelTitle", [], "method"), "html", null, true);
            echo "\">";
            echo $this->getAttribute(($context["this"] ?? null), "getLabel", [], "method");
            echo "</span></a>
  ";
        }
        // line 14
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "menu/account/link.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 14,  78 => 12,  75 => 11,  58 => 9,  36 => 7,  34 => 6,  30 => 4,);
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
 # Quick menu link
 #}

<div class=\"line\">
  {% if this.getLink() %}
    <a href=\"{{ this.getLink() }}\" class=\"link\"{% if this.getTooltip() %} title=\"{{ this.getTooltip() }}\"{% endif %}{% if this.getBlankPage() %} target=\"_blank\"{% endif %}>{{ this.getIcon()|raw }}{% if this.getTitle() %}<span>{{ this.getTitle()|raw }}</span>{% endif %}</a>
  {% else %}
    <span class=\"link\"{% if this.getTooltip() %} title=\"{{ this.getTooltip() }}\"{% endif %}>{{ this.getIcon()|raw }}{% if this.getTitle() %}<span>{{ this.getTitle()|raw }}</span>{% endif %}</span>
  {% endif %}
  {% if this.getLabel() %}
    <a href=\"{{ this.getLabelLink() }}\" class=\"label\"><span title=\"{{ this.getLabelTitle() }}\">{{ this.getLabel()|raw }}</span></a>
  {% endif %}
</div>
", "menu/account/link.twig", "/mff/xcart/skins/admin/menu/account/link.twig");
    }
}
