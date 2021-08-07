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

/* modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.admin.twig */
class __TwigTemplate_d4bdf7556ac01f934b1e65bf1f46957047fa801ac1e896995c97a2b977c1f64a extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\Button\\ThemeTweakerTab", "svg" => "modules/XC/ThemeTweaker/themetweaker_panel/icons/back.svg", "label" => "Admin panel", "style" => ("themetweaker-tab themetweaker-tab_admin " . $this->getAttribute(        // line 10
($context["this"] ?? null), "getTabClass", [0 => "admin"], "method")), "attributes" => ["@click" => "goAdminPanel"]]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.admin.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 10,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{##
 # Layout editor panel
 #
 # @ListChild(list=\"themetweaker-panel--tabs\", weight=\"0\")
 #}

{{ widget('XLite\\\\Module\\\\XC\\\\ThemeTweaker\\\\View\\\\Button\\\\ThemeTweakerTab',
  svg='modules/XC/ThemeTweaker/themetweaker_panel/icons/back.svg',
  label=\"Admin panel\",
  style=\"themetweaker-tab themetweaker-tab_admin \" ~ this.getTabClass('admin'),
  attributes={\"@click\": \"goAdminPanel\"}) }}", "modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.admin.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.admin.twig");
    }
}
