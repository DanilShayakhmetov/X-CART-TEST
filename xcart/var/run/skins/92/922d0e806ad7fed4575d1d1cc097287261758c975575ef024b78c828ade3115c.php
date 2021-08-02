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

/* modules/XC/ThemeTweaker/themetweaker/custom_css/css_editor.twig */
class __TwigTemplate_e6fca578599da5e6c6d9bc68d88adc89207f5cf43f0c5d09605275e69ab335d5 extends \XLite\Core\Templating\Twig\Template
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
<xlite-custom-css inline-template :initial=\"";
        // line 5
        echo (($this->getAttribute(($context["this"] ?? null), "isCustomCssEnabled", [], "method")) ? ("true") : ("false"));
        echo "\">
    <div class=\"custom-css-section themetweaker-section\">
      ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\FormField\\Textarea\\CodeMirror", "attributes" => ["v-pre" => "v-pre", "data-css-editor" => "data-css-editor"], "fieldOnly" => true, "formControl" => false, "codeMode" => "css", "value" => $this->getAttribute(        // line 16
($context["this"] ?? null), "getCustomCss", [], "method")]]), "html", null, true);
        // line 17
        echo "
      <script type=\"text/plain\" data-css-content>";
        // line 18
        echo $this->getAttribute(($context["this"] ?? null), "getCustomCss", [], "method");
        echo "</script>
    </div>
</xlite-custom-css>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker/custom_css/css_editor.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 18,  41 => 17,  39 => 16,  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/ThemeTweaker/themetweaker/custom_css/css_editor.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/custom_css/css_editor.twig");
    }
}
