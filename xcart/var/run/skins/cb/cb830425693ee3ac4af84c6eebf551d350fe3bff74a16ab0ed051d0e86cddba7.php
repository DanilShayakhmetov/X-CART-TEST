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

/* form_field/form_field.twig */
class __TwigTemplate_1cbe29ec73c623cc63616d379c119719aa0398311bf3b7718bb1c83c0e907d03 extends \XLite\Core\Templating\Twig\Template
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
        if ( !$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "fieldOnly"], "method")) {
            // line 6
            echo "  ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($this->getAttribute(($context["this"] ?? null), "getFieldLabelTemplate", [], "method"));            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate($this->getAttribute(($context["this"] ?? null), "getFieldLabelTemplate", [], "method"), "form_field/form_field.twig", 6)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
        }
        // line 8
        echo "
<div class=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getValueContainerClass", [], "method"), "html", null, true);
        echo "\">
  ";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath((($this->getAttribute(        // line 10
($context["this"] ?? null), "getDir", [], "method") . "/") . $this->getAttribute(($context["this"] ?? null), "getFieldTemplate", [], "method")));        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        $this->loadTemplate((($this->getAttribute(($context["this"] ?? null), "getDir", [], "method") . "/") . $this->getAttribute(($context["this"] ?? null), "getFieldTemplate", [], "method")), "form_field/form_field.twig", 10)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 11
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "hasHelp", [], "method")) {
            // line 12
            echo "    ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Tooltip", "text" => call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "help"], "method")]), "helpWidget" => $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "helpWidget"], "method"), "isImageTag" => "true", "className" => "help-icon"]]), "html", null, true);
            echo "
  ";
        }
        // line 14
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "comment"], "method")) {
            // line 15
            echo "    <div class=\"form-field-comment ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFieldId", [], "method"), "html", null, true);
            echo "-comment\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "comment"], "method")]), "html", null, true);
            echo "</div>
  ";
        }
        // line 17
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getFormFieldJSData", [], "method")) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getFormFieldJSData", [], "method")], "method"), "html", null, true);
        }
        // line 18
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getInlineJSCode", [], "method")) {
            // line 19
            echo "    <script>";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getInlineJSCode", [], "method"), "html", null, true);
            echo "</script>
  ";
        }
        // line 21
        echo "</div>

";
        // line 23
        if ( !$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "fieldOnly"], "method")) {
            // line 24
            echo "  <div class=\"clear\"></div>
";
        }
    }

    public function getTemplateName()
    {
        return "form_field/form_field.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 24,  102 => 23,  98 => 21,  92 => 19,  89 => 18,  84 => 17,  76 => 15,  73 => 14,  67 => 12,  64 => 11,  54 => 10,  50 => 9,  47 => 8,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "form_field/form_field.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/form_field/form_field.twig");
    }
}
