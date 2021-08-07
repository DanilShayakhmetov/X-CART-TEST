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

/* menu/account/top_menu_node.twig */
class __TwigTemplate_90efa5f15854d097f460dd10f000144aaf7aa5aa2ce5ca6ca10cf0dde4a98a60 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"account-menu menu\">
  <div class=\"title\">
    <div class=\"icon\">";
        // line 6
        echo $this->getAttribute(($context["this"] ?? null), "getSVGImage", [0 => "images/fa-male-user.svg"], "method");
        echo "</div>
    <i class=\"fa fa-angle-right\"></i>
  </div>
  <div class=\"box\">";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Menu\\Admin\\Account\\Menu"]]), "html", null, true);
        echo "</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "menu/account/top_menu_node.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 9,  34 => 6,  30 => 4,);
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
 # Account top menu node
 #}
<div class=\"account-menu menu\">
  <div class=\"title\">
    <div class=\"icon\">{{ this.getSVGImage('images/fa-male-user.svg')|raw }}</div>
    <i class=\"fa fa-angle-right\"></i>
  </div>
  <div class=\"box\">{{ widget('XLite\\\\View\\\\Menu\\\\Admin\\\\Account\\\\Menu') }}</div>
</div>
", "menu/account/top_menu_node.twig", "/mff/xcart/skins/admin/menu/account/top_menu_node.twig");
    }
}
