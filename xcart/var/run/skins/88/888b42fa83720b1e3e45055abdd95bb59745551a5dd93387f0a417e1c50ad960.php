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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.labels_editor.twig */
class __TwigTemplate_8bddf2703c29cb01a6e7688b7820c84d7c133d51258c4f8bc13655d8bbc479c3 extends \XLite\Core\Templating\Twig\Template
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
";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\Button\\ThemeTweakerTab", "svg" => "modules/XC/ThemeTweaker/themetweaker_panel/icons/labels.svg", "label" => "Labels editor", "style" => ("themetweaker-tab themetweaker-tab_labels_editor " . $this->getAttribute(        // line 10
($context["this"] ?? null), "getTabClass", [0 => "labels_editor"], "method")), "disabled" =>  !$this->getAttribute(        // line 11
($context["this"] ?? null), "isTabAvailable", [0 => "labels_editor"], "method"), "disabledTooltip" => $this->getAttribute(        // line 12
($context["this"] ?? null), "getTabDisabledTooltip", [0 => "labels_editor"], "method"), "attributes" => $this->getAttribute(        // line 13
($context["this"] ?? null), "getTabAttributes", [0 => "labels_editor"], "method")]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.labels_editor.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 13,  36 => 12,  35 => 11,  34 => 10,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.labels_editor.twig", "");
    }
}
