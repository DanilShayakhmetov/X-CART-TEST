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

/* left_menu/extensions/link.twig */
class __TwigTemplate_7b837bbca1da7ca30e0dda5873c127418a6a26ebacf27f528206ac16c27c117e extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"line";
        if ($this->getAttribute(($context["this"] ?? null), "getLabel", [], "method")) {
            echo " with-label";
        }
        echo "\">
  <a href=\"";
        // line 5
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
        echo "><span class=\"icon\">";
        echo $this->getAttribute(($context["this"] ?? null), "getIcon", [], "method");
        echo "</span>";
        if ($this->getAttribute(($context["this"] ?? null), "getTitle", [], "method")) {
            echo "<span class=\"title\">";
            echo $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method");
            echo "</span>";
        }
        echo "</a>

  ";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "getActionWidget", [], "method")) {
            // line 8
            echo "    <div class=\"action-widget\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "getActionWidget", [], "method"), "display", [], "method"), "html", null, true);
            echo "</div>
  ";
        }
        // line 10
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "left_menu/extensions/link.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  67 => 10,  61 => 8,  59 => 7,  37 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "left_menu/extensions/link.twig", "/mff/xcart/skins/admin/left_menu/extensions/link.twig");
    }
}
