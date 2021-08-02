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

/* file_uploader/body.twig */
class __TwigTemplate_27d247d9f8846930199292dbc3b84c508862db3a1075c12283b9a9d4b0f3c28c extends \XLite\Core\Templating\Twig\Template
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
<xlite-file-uploader inline-template ";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "hasMultipleSelector", [], "method")) {
            echo ":multiple=\"true\"";
        }
        // line 6
        echo "                     help-message=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getHelp", [], "method")]), "html", null, true);
        echo "\">
  <div class=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getDivStyle", [], "method"), "html", null, true);
        echo "\"
       data-object-id=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getObjectId", [], "method"), "html", null, true);
        echo "\"
       data-max-width=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getMaxWidth", [], "method"), "html", null, true);
        echo "\"
       data-max-height=\"";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getMaxHeight", [], "method"), "html", null, true);
        echo "\"
       data-name=\"";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
        echo "\"
       v-data='{ \"basePath\": \"";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getVModel", [], "method"), "html", null, true);
        echo "\",
       \"isRemovable\": \"";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "isRemovable", [], "method"), "html", null, true);
        echo "\",
       \"isTemporary\": \"";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "isTemporary", [], "method"), "html", null, true);
        echo "\",
       \"isImage\": \"";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "isImage", [], "method"), "html", null, true);
        echo "\",
       \"hasFile\": \"";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "hasFile", [], "method"), "html", null, true);
        echo "\",
       \"error\": \"";
        // line 17
        echo (($this->getAttribute(($context["this"] ?? null), "getMessage", [], "method")) ? ("1") : (""));
        echo "\",
       \"defaultErrorMessage\": \"";
        // line 18
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getErrorMessageDefault", [], "method")]), "js"), "html", null, true);
        echo "\",
       \"initialAlt\":\"";
        // line 19
        if ($this->getAttribute(($context["this"] ?? null), "hasAlt", [], "method")) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAlt", [], "method"), "js"), "html", null, true);
        }
        echo "\",
       \"realErrorMessage\": \"";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getMessage", [], "method")]), "js"), "html", null, true);
        echo "\"}'>
    ";
        // line 21
        if ($this->getAttribute(($context["this"] ?? null), "isRemovable", [], "method")) {
            // line 22
            echo "      <input type=\"checkbox\" name=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "[delete]\" v-model=\"delete\" value=\"1\"
             class=\"input-delete\"
             v-if=\"isRemovable\"
             v-data='{ \"delete\": false }'/>
    ";
        }
        // line 27
        echo "    ";
        if ($this->getAttribute(($context["this"] ?? null), "isMultiple", [], "method")) {
            // line 28
            echo "      <input type=\"hidden\" name=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "[position]\" v-model=\"position\"
             value=\"";
            // line 29
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPosition", [], "method"), "html", null, true);
            echo "\"
             class=\"input-position\"/>
    ";
        }
        // line 32
        echo "    ";
        if ($this->getAttribute(($context["this"] ?? null), "isTemporary", [], "method")) {
            // line 33
            echo "      <input type=\"hidden\" name=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "[temp_id]\" v-model=\"temp_id\"
             value=\"";
            // line 34
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "object", []), "id", []), "html", null, true);
            echo "\"
             v-if=\"isTemporary\"
             class=\"input-temp-id\"/>
    ";
        }
        // line 38
        echo "    <a href=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLink", [], "method"), "html", null, true);
        echo "\" class=\"link\" data-toggle=\"dropdown\">
      <i class=\"icon fa fa-camera\" v-if=\"isDisplayCamera\"></i>
      <i class=\"icon fa warning fa-exclamation-triangle\" v-if=\"errorMessage\"></i>
      <div class=\"preview\" v-if=\"isDisplayPreview\">
        ";
        // line 42
        echo $this->getAttribute(($context["this"] ?? null), "getPreview", [], "method");
        echo "
      </div>
      <div :class=\"error ? 'error' : 'help'\" v-html=\"message\" v-if=\"shouldShowMessage\"></div>
      <div class=\"icon\">
        <i class=\"";
        // line 46
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getIconStyle", [], "method"), "html", null, true);
        echo "\"></i>
      </div>
    </a>
    ";
        // line 49
        if ($this->getAttribute(($context["this"] ?? null), "hasAlt", [], "method")) {
            // line 50
            echo "      ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("file_uploader/parts/widget.alt.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate("file_uploader/parts/widget.alt.twig", "file_uploader/body.twig", 50)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 51
            echo "    ";
        }
        // line 52
        echo "    <ul class=\"dropdown-menu\" role=\"menu\">
      ";
        // line 53
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "file-uploader.menu"]]), "html", null, true);
        echo "
    </ul>
  </div>
</xlite-file-uploader>
";
    }

    public function getTemplateName()
    {
        return "file_uploader/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  180 => 53,  177 => 52,  174 => 51,  163 => 50,  161 => 49,  155 => 46,  148 => 42,  140 => 38,  133 => 34,  128 => 33,  125 => 32,  119 => 29,  114 => 28,  111 => 27,  102 => 22,  100 => 21,  96 => 20,  90 => 19,  86 => 18,  82 => 17,  78 => 16,  74 => 15,  70 => 14,  66 => 13,  62 => 12,  58 => 11,  54 => 10,  50 => 9,  46 => 8,  42 => 7,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "file_uploader/body.twig", "/mff/xcart/skins/common/file_uploader/body.twig");
    }
}
