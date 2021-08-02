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

/* modules/XC/ThemeTweaker/themetweaker/inline_editable/panel.twig */
class __TwigTemplate_ae475ab3e8cc6367ce46e808df39ad2152c337b6b01a447c39dadbad6f6cf0b4 extends \XLite\Core\Templating\Twig\Template
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
<xlite-inline-editor inline-template show-tinymce-warning=\"";
        // line 5
        echo (($this->getAttribute(($context["this"] ?? null), "isTinymceWarningVisible", [], "method")) ? ("true") : ("false"));
        echo "\">
    <div id=\"inline-editor-panel\" class=\"inline-editor--initial themetweaker-sections\">
        ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "inline_editor"]]), "html", null, true);
        echo "
    </div>
</xlite-inline-editor>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker/inline_editable/panel.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/ThemeTweaker/themetweaker/inline_editable/panel.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/inline_editable/panel.twig");
    }
}
