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

/* body/header.twig */
class __TwigTemplate_31fafc460f8866e4670dbeb7086398f2389c2e3401f02faae3c0ed4a76c225a5 extends \XLite\Core\Templating\Twig\Template
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
<div id=\"header-wrapper\">
  ";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.header_wrapper"]]), "html", null, true);
        echo "
  <div id=\"header\">
    <div class=\"left-menu-header\">
      ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.header.left"]]), "html", null, true);
        echo "
    </div>
    <div class=\"main-header\">
      ";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.header"]]), "html", null, true);
        echo "
    </div>
    <div class=\"main-header-right\">
      ";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.header.right"]]), "html", null, true);
        echo "
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "body/header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 15,  46 => 12,  40 => 9,  34 => 6,  30 => 4,);
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
 # Header
 #}

<div id=\"header-wrapper\">
  {{ widget_list('admin.main.page.header_wrapper') }}
  <div id=\"header\">
    <div class=\"left-menu-header\">
      {{ widget_list('admin.main.page.header.left') }}
    </div>
    <div class=\"main-header\">
      {{ widget_list('admin.main.page.header') }}
    </div>
    <div class=\"main-header-right\">
      {{ widget_list('admin.main.page.header.right') }}
    </div>
  </div>
</div>
", "body/header.twig", "/mff/xcart/skins/admin/body/header.twig");
    }
}
