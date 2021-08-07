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

/* top_links/version_notes/parts/upgrade.twig */
class __TwigTemplate_6678927726084a485c98ed182da8c1bb32c7f146ce159050be7b1729aca0dd11 extends \XLite\Core\Templating\Twig\Template
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
<div ";
        // line 5
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getContainerTagAttributes", [], "method")], "method");
        echo ">
  <div class=\"box\">
    <span class=\"warning\">";
        // line 7
        echo $this->getAttribute(($context["this"] ?? null), "getSVGImage", [0 => "images/info.svg"], "method");
        echo "</span>
    ";
        // line 8
        $context["url"] = $this->getAttribute(($context["this"] ?? null), "getUpgradeUrl", [], "method");
        // line 9
        echo "    ";
        if ($this->getAttribute(($context["this"] ?? null), "hasUpgrades", [], "method")) {
            // line 10
            echo "      <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Upgrade available"]), "html", null, true);
            echo " (";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getDescription", [], "method"), "html", null, true);
            echo ")</a>
    ";
        } else {
            // line 12
            echo "      <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Updates for the X-Cart core and/or installed modules are available"]), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Updates are available"]), "html", null, true);
            echo " (";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getDescription", [], "method"), "html", null, true);
            echo ")</a>
    ";
        }
        // line 14
        echo "    <a class=\"close\">";
        echo $this->getAttribute(($context["this"] ?? null), "displaySVGImage", [0 => "images/icon-close-round.svg"], "method");
        echo "</a>
  </div>
  <div class=\"corner-box\">
    <span class=\"corner\"><a class=\"warning\">";
        // line 17
        echo $this->getAttribute(($context["this"] ?? null), "getSVGImage", [0 => "images/info.svg"], "method");
        echo "</a></span>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "top_links/version_notes/parts/upgrade.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 17,  69 => 14,  57 => 12,  47 => 10,  44 => 9,  42 => 8,  38 => 7,  33 => 5,  30 => 4,);
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
 # \"Upgrade core\" link
 #}

<div {{ this.printTagAttributes(this.getContainerTagAttributes())|raw }}>
  <div class=\"box\">
    <span class=\"warning\">{{ this.getSVGImage('images/info.svg')|raw }}</span>
    {% set url = this.getUpgradeUrl() %}
    {% if this.hasUpgrades() %}
      <a href=\"{{ url }}\">{{ t('Upgrade available') }} ({{ this.getDescription() }})</a>
    {% else %}
      <a href=\"{{ url }}\" title=\"{{ t('Updates for the X-Cart core and/or installed modules are available') }}\">{{ t('Updates are available') }} ({{ this.getDescription() }})</a>
    {% endif %}
    <a class=\"close\">{{ this.displaySVGImage('images/icon-close-round.svg')|raw }}</a>
  </div>
  <div class=\"corner-box\">
    <span class=\"corner\"><a class=\"warning\">{{ this.getSVGImage('images/info.svg')|raw }}</a></span>
  </div>
</div>
", "top_links/version_notes/parts/upgrade.twig", "/mff/xcart/skins/admin/top_links/version_notes/parts/upgrade.twig");
    }
}
