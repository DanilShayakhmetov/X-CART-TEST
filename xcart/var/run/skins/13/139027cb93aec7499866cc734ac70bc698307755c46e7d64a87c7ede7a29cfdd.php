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

/* modules/XC/ThemeTweaker/layout/content/main.center.hidden.twig */
class __TwigTemplate_ff81fed303926869b8a02cc816febcb3dbbbb732404a1c494eac9653cb16cf4a extends \XLite\Core\Templating\Twig\Template
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
<div id=\"hidden-sidebar-content\" class=\"column\">
  ";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\Layout\\Customer\\SidebarHidden", "group" => "center"]]), "html", null, true);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/layout/content/main.center.hidden.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 8,  30 => 6,);
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
 # Center box
 #
 # @ListChild (list=\"layout.main.center\", weight=\"0\")
 #}

<div id=\"hidden-sidebar-content\" class=\"column\">
  {{ widget('XLite\\\\Module\\\\XC\\\\ThemeTweaker\\\\View\\\\Layout\\\\Customer\\\\SidebarHidden', group='center') }}
</div>
", "modules/XC/ThemeTweaker/layout/content/main.center.hidden.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/layout/content/main.center.hidden.twig");
    }
}
