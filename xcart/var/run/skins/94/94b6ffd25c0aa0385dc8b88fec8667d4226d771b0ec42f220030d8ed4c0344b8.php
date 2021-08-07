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

/* layout/header/mobile_header_parts/navbar_menu.twig */
class __TwigTemplate_55f572bc4e4a258b7e0ad223a08cf72c0abb5c0dec366454ad01a472a6b75e63 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isSlidebar", [])) {
            // line 7
            echo "  <li>
    <div class=\"Panel\" id=\"account-navbar-panel\">
      <ul class=\"Inset\">
        ";
            // line 10
            ob_start();
            // line 11
            echo "          ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "slidebar.navbar.account"]]), "html", null, true);
            echo "
        ";
            $context["account_items"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
            // line 13
            echo "
        ";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["account_items"] ?? null), "html", null, true);
            echo "
        ";
            // line 15
            if ( !twig_test_empty(twig_trim_filter(($context["account_items"] ?? null)))) {
                // line 16
                echo "          <li class=\"mm-divider\"></li>
        ";
            }
            // line 18
            echo "        ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "slidebar.navbar.account.additional-menu"]]), "html", null, true);
            echo "
      </ul>
    </div>
    ";
            // line 21
            if ($this->getAttribute(($context["this"] ?? null), "getViewList", [0 => "slidebar.navbar.settings"], "method")) {
                // line 22
                echo "      <div class=\"Panel\" id=\"settings-navbar-panel\">
        <ul class=\"Inset\">
          ";
                // line 24
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "slidebar.navbar.settings"]]), "html", null, true);
                echo "
        </ul>
      </div>
    ";
            }
            // line 28
            echo "  </li>
";
        }
    }

    public function getTemplateName()
    {
        return "layout/header/mobile_header_parts/navbar_menu.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 28,  71 => 24,  67 => 22,  65 => 21,  58 => 18,  54 => 16,  52 => 15,  48 => 14,  45 => 13,  39 => 11,  37 => 10,  32 => 7,  30 => 6,);
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
 # Navbar menu
 #
 # @ListChild (list=\"header.menu\", weight=\"2000\")
 #}
{% if this.isSlidebar %}
  <li>
    <div class=\"Panel\" id=\"account-navbar-panel\">
      <ul class=\"Inset\">
        {% set account_items %}
          {{ widget_list('slidebar.navbar.account') }}
        {% endset %}

        {{ account_items }}
        {% if not account_items|trim is empty %}
          <li class=\"mm-divider\"></li>
        {% endif %}
        {{ widget_list('slidebar.navbar.account.additional-menu') }}
      </ul>
    </div>
    {% if this.getViewList('slidebar.navbar.settings') %}
      <div class=\"Panel\" id=\"settings-navbar-panel\">
        <ul class=\"Inset\">
          {{ widget_list('slidebar.navbar.settings') }}
        </ul>
      </div>
    {% endif %}
  </li>
{% endif %}
", "layout/header/mobile_header_parts/navbar_menu.twig", "/mff/xcart/skins/crisp_white/customer/layout/header/mobile_header_parts/navbar_menu.twig");
    }
}
