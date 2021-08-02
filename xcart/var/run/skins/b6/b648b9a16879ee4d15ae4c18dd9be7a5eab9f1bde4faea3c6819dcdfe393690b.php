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

/* items_list/model/table/parts/head.cell.twig */
class __TwigTemplate_e9f90190f7133672b1b30a26ef1d5fe4e86df41f65668d580e8a9ea25c28ad0d extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["column"] ?? null), "columnSelector", [])) {
            // line 6
            echo "  <div>
";
        }
        // line 8
        echo "
";
        // line 9
        if ($this->getAttribute(($context["column"] ?? null), "headTemplate", [])) {
            // line 10
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, ["template" => $this->getAttribute(($context["column"] ?? null), "headTemplate", []), "column" => ($context["column"] ?? null)]]), "html", null, true);
            echo "
";
        } else {
            // line 12
            if ($this->getAttribute(($context["column"] ?? null), "sort", [])) {
                // line 13
                echo "  <a
    href=\"";
                // line 14
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, $this->getAttribute(($context["this"] ?? null), "getTarget", [], "method"), "", ["sortBy" => $this->getAttribute(($context["column"] ?? null), "sort", []), "sortOrder" => $this->getAttribute(($context["this"] ?? null), "getSortDirectionNext", [0 => ($context["column"] ?? null)], "method")]]), "html", null, true);
                echo "\"
    data-sort=\"";
                // line 15
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["column"] ?? null), "sort", []), "html", null, true);
                echo "\"
    data-direction=\"";
                // line 16
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSortOrder", [], "method"), "html", null, true);
                echo "\"
    class=\"";
                // line 17
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSortLinkClass", [0 => ($context["column"] ?? null)], "method"), "html", null, true);
                echo "\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["column"] ?? null), "name", []), "html", null, true);
                echo "</a>
  ";
                // line 18
                if ($this->getAttribute(($context["this"] ?? null), "isColumnSorted", [0 => ($context["column"] ?? null)], "method")) {
                    // line 19
                    echo "  ";
                    if (("desc" == $this->getAttribute(($context["this"] ?? null), "getSortOrder", [], "method"))) {
                        // line 20
                        echo "    <i class=\"dir desc-order\"></i>
  ";
                    }
                    // line 22
                    echo "  ";
                    if (("asc" == $this->getAttribute(($context["this"] ?? null), "getSortOrder", [], "method"))) {
                        // line 23
                        echo "    <i class=\"dir asc-order\"></i>
  ";
                    }
                    // line 25
                    echo "  ";
                }
            } else {
                // line 27
                echo "  <div class=\"table-header\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["column"] ?? null), "name", []), "html", null, true);
                echo "</div>
";
            }
            // line 29
            echo "  ";
            if ($this->getAttribute(($context["column"] ?? null), "headHelp", [])) {
                // line 30
                echo "    <div class=\"help-wrapper\">
      ";
                // line 31
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Tooltip", "id" => "menu-links-help-text", "text" => $this->getAttribute(($context["column"] ?? null), "headHelp", []), "isImageTag" => "true", "className" => "help-small-icon"]]), "html", null, true);
                echo "
    </div>
  ";
            }
        }
        // line 35
        if (($this->getAttribute(($context["column"] ?? null), "subheader", []) || $this->getAttribute(($context["this"] ?? null), "hasSubheaders", [], "method"))) {
            // line 36
            echo "  <div class=\"subheader\">";
            if ($this->getAttribute(($context["column"] ?? null), "subheader", [])) {
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["column"] ?? null), "subheader", []), "html", null, true);
            }
            echo "</div>
";
        }
        // line 38
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => $this->getAttribute(($context["this"] ?? null), "getCellListNamePart", [0 => "head", 1 => ($context["column"] ?? null)], "method"), "type" => "inherited", "column" => ($context["column"] ?? null)]]), "html", null, true);
        echo "
";
        // line 39
        if ($this->getAttribute(($context["column"] ?? null), "columnSelector", [])) {
            // line 40
            echo "  <input type=\"checkbox\"
         class=\"selectAll not-significant\"
         autocomplete=\"off\" />
";
        }
        // line 44
        echo "
";
        // line 45
        if ($this->getAttribute(($context["column"] ?? null), "columnSelector", [])) {
            // line 46
            echo "    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "items_list/model/table/parts/head.cell.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  139 => 46,  137 => 45,  134 => 44,  128 => 40,  126 => 39,  122 => 38,  114 => 36,  112 => 35,  105 => 31,  102 => 30,  99 => 29,  93 => 27,  89 => 25,  85 => 23,  82 => 22,  78 => 20,  75 => 19,  73 => 18,  67 => 17,  63 => 16,  59 => 15,  55 => 14,  52 => 13,  50 => 12,  44 => 10,  42 => 9,  39 => 8,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/model/table/parts/head.cell.twig", "/mff/xcart/skins/admin/items_list/model/table/parts/head.cell.twig");
    }
}
