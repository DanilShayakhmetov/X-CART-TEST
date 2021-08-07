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

/* layout/header/header.bar.links.logged.twig */
class __TwigTemplate_d195123df01160101fe99715d52c547a685e5ac811314f5b41bce33a67dba454 extends \XLite\Core\Templating\Twig\Template
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
        if (($this->getAttribute(($context["this"] ?? null), "isLogged", [], "method") &&  !$this->getAttribute(($context["this"] ?? null), "isCheckoutLayout", [], "method"))) {
            // line 8
            echo "  <div class=\"dropdown header_bar-my_account\">
    <a data-toggle=\"dropdown\" data-target=\"#\">";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["My account"]), "html", null, true);
            echo "</a>
    <ul class=\"account-links dropdown-menu\">
      ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "layout.header.bar.links.logged"]]), "html", null, true);
            echo "
    </ul>
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "layout/header/header.bar.links.logged.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 11,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
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
 # Header bar account links
 #
 # @ListChild (list=\"layout.header.bar\", weight=\"100\")
 #}

{% if this.isLogged() and not this.isCheckoutLayout() %}
  <div class=\"dropdown header_bar-my_account\">
    <a data-toggle=\"dropdown\" data-target=\"#\">{{ t('My account') }}</a>
    <ul class=\"account-links dropdown-menu\">
      {{ widget_list('layout.header.bar.links.logged') }}
    </ul>
  </div>
{% endif %}
", "layout/header/header.bar.links.logged.twig", "/mff/xcart/skins/crisp_white/customer/layout/header/header.bar.links.logged.twig");
    }
}
