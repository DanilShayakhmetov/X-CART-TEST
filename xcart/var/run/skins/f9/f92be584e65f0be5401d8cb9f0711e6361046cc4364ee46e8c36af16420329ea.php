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
class __TwigTemplate_cb23b7af49c6c04d0bddb54cd91fdf9ca1900b6575d46701eea26ded6dd4e191 extends \XLite\Core\Templating\Twig\Template
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
  <div class=\"input-internal-wrapper\">
    ";
        // line 11
        if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "editOnClick"], "method")) {
            // line 12
            echo "      <div ";
            echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getContainerAttributes", [], "method")], "method");
            echo ">
        <div ";
            // line 13
            echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getViewContainerAttributes", [], "method")], "method");
            echo ">";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($this->getAttribute(($context["this"] ?? null), "getViewTemplate", [], "method"));            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate($this->getAttribute(($context["this"] ?? null), "getViewTemplate", [], "method"), "form_field/form_field.twig", 13)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            echo "</div>
        <div ";
            // line 14
            echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getFieldContainerAttributes", [], "method")], "method");
            echo ">
          ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath((($this->getAttribute(            // line 15
($context["this"] ?? null), "getDir", [], "method") . "/") . $this->getAttribute(($context["this"] ?? null), "getFieldTemplate", [], "method")));            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate((($this->getAttribute(($context["this"] ?? null), "getDir", [], "method") . "/") . $this->getAttribute(($context["this"] ?? null), "getFieldTemplate", [], "method")), "form_field/form_field.twig", 15)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 16
            echo "        </div>
      </div>
    ";
        } else {
            // line 19
            echo "      ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath((($this->getAttribute(($context["this"] ?? null), "getDir", [], "method") . "/") . $this->getAttribute(($context["this"] ?? null), "getFieldTemplate", [], "method")));            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate((($this->getAttribute(($context["this"] ?? null), "getDir", [], "method") . "/") . $this->getAttribute(($context["this"] ?? null), "getFieldTemplate", [], "method")), "form_field/form_field.twig", 19)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 20
            echo "    ";
        }
        // line 21
        echo "    ";
        if ($this->getAttribute(($context["this"] ?? null), "hasHelp", [], "method")) {
            // line 22
            echo "      <div class=\"help-wrapper\">
        ";
            // line 23
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Tooltip", "text" => call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "help"], "method")]), "helpWidget" => $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "helpWidget"], "method"), "isImageTag" => "true", "className" => "help-icon"]]), "html", null, true);
            echo "
      </div>
    ";
        }
        // line 26
        echo "  </div>
  ";
        // line 27
        if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "linkHref"], "method")) {
            // line 28
            echo "    ";
            if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "linkImg"], "method")) {
                // line 29
                echo "      <img src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "linkImg"], "method"), "html", null, true);
                echo "\" class=\"form-field-link-img\" alt=\"\" height=\"20\">
    ";
            }
            // line 31
            echo "    <a class=\"form-field-link ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFieldId", [], "method"), "html", null, true);
            echo "-link\" href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "linkHref"], "method"), "html", null, true);
            echo "\">
      ";
            // line 32
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "linkText"], "method")]);
            echo "
    </a>
  ";
        }
        // line 35
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "comment"], "method")) {
            // line 36
            echo "    <div class=\"form-field-comment ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFieldId", [], "method"), "html", null, true);
            echo "-comment\">";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "comment"], "method")]);
            echo "</div>
  ";
        }
        // line 38
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getFormFieldJSData", [], "method")) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getFormFieldJSData", [], "method")], "method"), "html", null, true);
        }
        // line 39
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getInlineJSCode", [], "method")) {
            // line 40
            echo "    <script type=\"text/javascript\">";
            echo $this->getAttribute(($context["this"] ?? null), "getInlineJSCode", [], "method");
            echo "</script>
  ";
        }
        // line 42
        echo "</div>

";
        // line 44
        if ( !$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "fieldOnly"], "method")) {
            // line 45
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
        return array (  179 => 45,  177 => 44,  173 => 42,  167 => 40,  164 => 39,  159 => 38,  151 => 36,  148 => 35,  142 => 32,  135 => 31,  129 => 29,  126 => 28,  124 => 27,  121 => 26,  115 => 23,  112 => 22,  109 => 21,  106 => 20,  95 => 19,  90 => 16,  80 => 15,  76 => 14,  62 => 13,  57 => 12,  55 => 11,  50 => 9,  47 => 8,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "form_field/form_field.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/form_field/form_field.twig");
    }
}
