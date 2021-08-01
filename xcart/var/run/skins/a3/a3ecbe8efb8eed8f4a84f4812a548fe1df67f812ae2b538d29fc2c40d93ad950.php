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

/* items_list/body.twig */
class __TwigTemplate_a44e90f15cfebb16bd37b08ec06ae9717d399dd0ec7deaf080b221441f894956 extends \XLite\Core\Templating\Twig\Template
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
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getWidgetTagAttributes", [], "method")], "method");
        echo ">
  ";
        // line 6
        $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getJSData", [], "method")], "method");
        // line 7
        echo "
  ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "isHeadVisible", [], "method")) {
            // line 9
            echo "    <div class=\"head-h2 ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getListHeadClass", [], "method"), "html", null, true);
            echo "\">";
            echo $this->getAttribute(($context["this"] ?? null), "getListHead", [], "method");
            echo "</div>
  ";
        }
        // line 11
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isPagerVisible", [], "method")) {
            // line 12
            echo "    <div class=\"list-pager\">";
            $this->getAttribute($this->getAttribute(($context["this"] ?? null), "pager", []), "display", [], "method");
            echo "</div>
  ";
        }
        // line 14
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isHeaderVisible", [], "method")) {
            // line 15
            echo "    <div class=\"list-header\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header", "type" => "inherited"]]), "html", null, true);
            echo "</div>
  ";
        }
        // line 17
        echo "
  ";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($this->getAttribute(        // line 18
($context["this"] ?? null), "getPageBodyTemplate", [], "method"));        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        $this->loadTemplate($this->getAttribute(($context["this"] ?? null), "getPageBodyTemplate", [], "method"), "items_list/body.twig", 18)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 19
        echo "
  ";
        // line 20
        if (($this->getAttribute(($context["this"] ?? null), "isPagerVisible", [], "method") && $this->getAttribute($this->getAttribute(($context["this"] ?? null), "pager", []), "isPagesListVisible", [], "method"))) {
            // line 21
            echo "    <div class=\"list-pager list-pager-bottom\">";
            $this->getAttribute($this->getAttribute(($context["this"] ?? null), "pager", []), "display", [], "method");
            echo "</div>
  ";
        }
        // line 23
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isFooterVisible", [], "method")) {
            // line 24
            echo "    <div class=\"list-footer\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "footer", "type" => "inherited"]]), "html", null, true);
            echo "</div>
  ";
        }
        // line 26
        echo "
  ";
        // line 27
        if ($this->getAttribute(($context["this"] ?? null), "isEmptyListTemplateVisible", [], "method")) {
            // line 28
            echo "    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($this->getAttribute(($context["this"] ?? null), "getEmptyListTemplate", [], "method"));            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate($this->getAttribute(($context["this"] ?? null), "getEmptyListTemplate", [], "method"), "items_list/body.twig", 28)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 29
            echo "  ";
        }
        // line 30
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "items_list/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  122 => 30,  119 => 29,  108 => 28,  106 => 27,  103 => 26,  97 => 24,  94 => 23,  88 => 21,  86 => 20,  83 => 19,  73 => 18,  70 => 17,  64 => 15,  61 => 14,  55 => 12,  52 => 11,  44 => 9,  42 => 8,  39 => 7,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/body.twig");
    }
}
