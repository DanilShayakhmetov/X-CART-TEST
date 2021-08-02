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

/* items_list/model/table/field.twig */
class __TwigTemplate_801074878e00a7ab016b2f21fdf3653b8780257a9c9399153911f3d3e8f59e01 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"plain-value ";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "isLink", [0 => $this->getAttribute(($context["this"] ?? null), "column", []), 1 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method")) {
            echo "link-value";
        }
        echo "\">
  ";
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "isLink", [0 => $this->getAttribute(($context["this"] ?? null), "column", []), 1 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method")) {
            // line 7
            echo "    <span class=\"value\">
      <a
        href=\"";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "buildEntityURL", [0 => $this->getAttribute(($context["this"] ?? null), "entity", []), 1 => $this->getAttribute(($context["this"] ?? null), "column", [])], "method"), "html", null, true);
            echo "\"
        ";
            // line 10
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "column", []), "noWrap", [])) {
                echo " title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getColumnValue", [0 => $this->getAttribute(($context["this"] ?? null), "column", []), 1 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method"), "html", null, true);
                echo "\"";
            }
            // line 11
            echo "        class=\"link\">";
            echo $this->getAttribute(($context["this"] ?? null), "getColumnValue", [0 => $this->getAttribute(($context["this"] ?? null), "column", []), 1 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method");
            echo "
      </a>
    </span>
  ";
        }
        // line 15
        echo "  ";
        if ( !$this->getAttribute(($context["this"] ?? null), "isLink", [0 => $this->getAttribute(($context["this"] ?? null), "column", []), 1 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method")) {
            // line 16
            echo "    <span class=\"value\">";
            echo $this->getAttribute(($context["this"] ?? null), "getColumnValue", [0 => $this->getAttribute(($context["this"] ?? null), "column", []), 1 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method");
            echo "</span>
  ";
        }
        // line 18
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "items_list/model/table/field.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 18,  66 => 16,  63 => 15,  55 => 11,  49 => 10,  45 => 9,  41 => 7,  39 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/model/table/field.twig", "/mff/xcart/skins/admin/items_list/model/table/field.twig");
    }
}
