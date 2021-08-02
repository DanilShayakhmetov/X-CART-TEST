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

/* modules/XC/ThemeTweaker/themetweaker/webmaster_mode/template_code.twig */
class __TwigTemplate_b058c0ab37a7125f0eb8a8aa0464ec4310920ab68c768f3c67740ebd3259df11 extends \XLite\Core\Templating\Twig\Template
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
<xlite-template-code inline-template :interface=\"interface\" :template=\"template\" :weight=\"weight\" :list=\"list\">
  <div class=\"xlite-template-code\" :class=\"classes\" v-data='";
        // line 6
        echo $this->getAttribute(($context["this"] ?? null), "getWidgetData", [], "method");
        echo "'>
    <div class=\"xlite-template-path\" v-if=\"template\"><span v-text=\"template\"></span></div>
    ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "getTemplatePath", [], "method")) {
            // line 9
            echo "      ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\FormField\\Textarea\\CodeMirror", "attributes" => ["v-pre" => "v-pre", "data-template-editor" => "data-template-editor"], "fieldOnly" => true, "formControl" => false, "codeMode" => "twig"]]), "html", null, true);
            // line 18
            echo "
      <script type=\"text/plain\" data-template-content>";
            // line 19
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTemplateContent", [], "method"), "html", null, true);
            echo "</script>
    ";
        }
        // line 21
        echo "  </div>
</xlite-template-code>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker/webmaster_mode/template_code.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 21,  47 => 19,  44 => 18,  41 => 9,  39 => 8,  34 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/ThemeTweaker/themetweaker/webmaster_mode/template_code.twig", "/mff/xcart/skins/common/modules/XC/ThemeTweaker/themetweaker/webmaster_mode/template_code.twig");
    }
}
