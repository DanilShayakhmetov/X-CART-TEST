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

/* modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.switcher.twig */
class __TwigTemplate_2e48af1d021fd14468b32d5a41fdd4b71619ff55b04879f9b3e1b2b287aff6b1 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"themetweaker-action themetweaker-action_switcher\"
     v-show=\"mode\" data-toggle=\"tooltip\" data-placement=\"top\"
     data-html=\"true\" title=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["themetweaker.shortcut.switcher"]), "html", null, true);
        echo "\"
     :class=\"switcherClasses\">
  ";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Checkbox\\OnOff", "label" => "{{{ switcherLabel }}}", "style" => "", "attributes" => [":checked" => "switcher", "@change" => "onSwitchChange", "data-panel-switcher" => "data-panel-switcher"]]]), "html", null, true);
        // line 17
        echo "
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.switcher.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 17,  40 => 11,  35 => 9,  30 => 6,);
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
 # @ListChild(list=\"themetweaker-panel--actions\", weight=\"100\")
 #}

<div class=\"themetweaker-action themetweaker-action_switcher\"
     v-show=\"mode\" data-toggle=\"tooltip\" data-placement=\"top\"
     data-html=\"true\" title=\"{{ t('themetweaker.shortcut.switcher') }}\"
     :class=\"switcherClasses\">
  {{ widget('XLite\\\\View\\\\FormField\\\\Input\\\\Checkbox\\\\OnOff',
      label=\"{{{ switcherLabel }}}\",
      style=\"\",
      attributes={
        \":checked\": \"switcher\",
        '@change': 'onSwitchChange',
        'data-panel-switcher': 'data-panel-switcher'}) }}
</div>", "modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.switcher.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/actions.switcher.twig");
    }
}
