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

/* /mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/list_item_parts/action_unhide.twig */
class __TwigTemplate_ebf77e1d002c78e10f2ae8b9b5e12eab3f09d31a1abd4170dcfa557f09f91e39 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\Button\\ListItemAction", 1 => ["tooltip" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Block is disabled"]), "style" => "list-item-action__unhide", "event" => "layout.unhide", "icon-style" => "fa-eye-slash"]]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/list_item_parts/action_unhide.twig";
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
        return new Source("", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/list_item_parts/action_unhide.twig", "");
    }
}
