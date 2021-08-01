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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/layout_options.twig */
class __TwigTemplate_33b1ef30de9adb8a9186522527a32a5083e086001971c9ecc9d65168aaca230b extends \XLite\Core\Templating\Twig\Template
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
        // line 6
        echo "
<xlite-layout-options inline-template :preset=\"preset\" :initial-reset-available=\"";
        // line 7
        echo (($this->getAttribute(($context["this"] ?? null), "isResetAvailable", [], "method")) ? ("true") : ("false"));
        echo "\">
\t<div class='layout-editor-layout-options'>
\t\t<div class=\"options-layout-header\">
\t\t\t<span>";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Storefront layout"]), "html", null, true);
        echo "</span>
\t\t\t<a class=\"reset-button\" :class=\"resetBtnClasses\" @click=\"resetLayout\">
\t\t\t\t<i class=\"icon\">";
        // line 12
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "modules/XC/ThemeTweaker/themetweaker/layout_editor/icons/return.svg"]);
        echo "</i>
\t\t\t\t<span>";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Reset layout"]), "html", null, true);
        echo "</span>
\t\t\t</a>
\t\t</div>

\t\t<div class=\"options-layout-content\">
\t\t\t";
        // line 18
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\FormField\\Select\\LayoutType", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Home page"]), "group" => twig_constant("XLite\\Core\\Layout::LAYOUT_GROUP_HOME")]]), "html", null, true);
        // line 20
        echo "
\t\t\t";
        // line 21
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\FormField\\Select\\LayoutType", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Other pages"]), "group" => twig_constant("XLite\\Core\\Layout::LAYOUT_GROUP_DEFAULT")]]), "html", null, true);
        // line 23
        echo "
\t\t</div>
\t\t";
        // line 25
        if ($this->getAttribute(($context["this"] ?? null), "isLogoConfigurable", [], "method")) {
            // line 26
            echo "\t\t<div class=\"logo-options-wrapper\">
\t\t\t<div class=\"logo-options-header\">
\t\t\t\t<span>";
            // line 28
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Logo & Favicon"]), "html", null, true);
            echo "</span>
\t\t\t</div>
\t\t\t<div class=\"logo-options-content\">
\t\t\t\t<div class=\"company-logo-uploader\">
\t\t\t\t\t";
            // line 32
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FileUploader", "object" => $this->getAttribute(            // line 33
($context["this"] ?? null), "getImageObject", [0 => "logo"], "method"), "maxWidth" => $this->getAttribute(            // line 34
($context["this"] ?? null), "getImageMaxWidth", [], "method"), "maxHeight" => $this->getAttribute(            // line 35
($context["this"] ?? null), "getImageMaxHeight", [], "method"), "isImage" => true, "fieldName" => "logo", "hasAlt" => true, "isViaUrlAllowed" => false]]), "html", null, true);
            // line 40
            echo "
\t\t\t\t\t<span class=\"option-label\">";
            // line 41
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["logo uploader"]), "html", null, true);
            echo "</span>
\t\t\t\t</div>
\t\t\t\t<div class=\"favicon-uploader\" data-extended=\"1\">
\t\t\t\t\t";
            // line 44
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FileUploader", "object" => $this->getAttribute(            // line 45
($context["this"] ?? null), "getImageObject", [0 => "favicon"], "method"), "maxWidth" => $this->getAttribute(            // line 46
($context["this"] ?? null), "getImageMaxWidth", [], "method"), "maxHeight" => $this->getAttribute(            // line 47
($context["this"] ?? null), "getImageMaxHeight", [], "method"), "isImage" => true, "fieldName" => "favicon", "hasAlt" => false, "isViaUrlAllowed" => false]]), "html", null, true);
            // line 52
            echo "
\t\t\t\t\t<span class=\"option-label\">";
            // line 53
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["favicon uploader"]), "html", null, true);
            echo "</span>
\t\t\t\t</div>
\t\t\t\t<div class=\"appleIcon-uploader\">
\t\t\t\t\t";
            // line 56
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FileUploader", "object" => $this->getAttribute(            // line 57
($context["this"] ?? null), "getImageObject", [0 => "appleIcon"], "method"), "maxWidth" => $this->getAttribute(            // line 58
($context["this"] ?? null), "getImageMaxWidth", [], "method"), "maxHeight" => $this->getAttribute(            // line 59
($context["this"] ?? null), "getImageMaxHeight", [], "method"), "isImage" => true, "fieldName" => "appleIcon", "hasAlt" => false, "isViaUrlAllowed" => false]]), "html", null, true);
            // line 64
            echo "
\t\t\t\t\t<span class=\"option-label\">";
            // line 65
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["app icon uploader"]), "html", null, true);
            echo "</span>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>
\t\t";
        }
        // line 70
        echo "\t</div>
</xlite-layout-options>";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/layout_options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 70,  116 => 65,  113 => 64,  111 => 59,  110 => 58,  109 => 57,  108 => 56,  102 => 53,  99 => 52,  97 => 47,  96 => 46,  95 => 45,  94 => 44,  88 => 41,  85 => 40,  83 => 35,  82 => 34,  81 => 33,  80 => 32,  73 => 28,  69 => 26,  67 => 25,  63 => 23,  61 => 21,  58 => 20,  56 => 18,  48 => 13,  44 => 12,  39 => 10,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/layout_options.twig", "");
    }
}
