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

/* modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.save.twig */
class __TwigTemplate_4ed6c0c8ca58848e2eb968991cac2909d48e6490ef92bbc5eab420879a7a1d23 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\Button\\ThemeTweakerButton", "label" => "Save", "style" => "themetweaker-action themetweaker-action_save", "attributes" => ["@click" => "onSaveClick", "v-show" => "mode", "data-panel-save-button" => "data-panel-save-button", ":class" => "saveButtonClasses", ":disabled" => "isSaveDisabled", "data-toggle" => "tooltip", "data-placement" => "top", "data-html" => "true", "title" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["themetweaker.shortcut.save"])]]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.save.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 7,  30 => 6,);
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
 # @ListChild(list=\"themetweaker-panel--actions\", weight=\"200\")
 #}

{{ widget('XLite\\\\Module\\\\XC\\\\ThemeTweaker\\\\View\\\\Button\\\\ThemeTweakerButton',
          label=\"Save\",
          style=\"themetweaker-action themetweaker-action_save\",
          attributes={
            \"@click\": \"onSaveClick\",
            \"v-show\": \"mode\",
            'data-panel-save-button': 'data-panel-save-button',
            ':class': 'saveButtonClasses',
            ':disabled': 'isSaveDisabled',
            'data-toggle': 'tooltip',
            'data-placement': 'top',
            'data-html': \"true\",
            'title': t('themetweaker.shortcut.save'),
          }) }}", "modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.save.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.save.twig");
    }
}
