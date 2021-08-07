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

/* modules/XC/ThemeTweaker/button/themetweaker-tab.twig */
class __TwigTemplate_56f7faeed3ba339077d613938704a5706128fec47ec68aba1f3f90ee95dbde83 extends \XLite\Core\Templating\Twig\Template
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
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getWrapperAttributes", [], "method")], "method");
        echo ">
<button ";
        // line 6
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getAttributes", [], "method")], "method");
        echo ">
  ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getCommentedData", [], "method")], "method"), "html", null, true);
        echo "
  ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "getSvgIcon", [], "method")) {
            // line 9
            echo "    ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displaySVGImage", [0 => $this->getAttribute(($context["this"] ?? null), "getSvgIcon", [], "method")], "method"), "html", null, true);
            echo "
  ";
        }
        // line 11
        echo "  <span>";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getButtonLabel", [], "method")]), "html", null, true);
        echo "</span>
</button>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/button/themetweaker-tab.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 11,  47 => 9,  45 => 8,  41 => 7,  37 => 6,  33 => 5,  30 => 4,);
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
 # Regular button
 #}

<div {{ this.printTagAttributes(this.getWrapperAttributes())|raw }}>
<button {{ this.printTagAttributes(this.getAttributes())|raw }}>
  {{ this.displayCommentedData(this.getCommentedData()) }}
  {% if this.getSvgIcon() %}
    {{ this.displaySVGImage(this.getSvgIcon()) }}
  {% endif %}
  <span>{{ t(this.getButtonLabel()) }}</span>
</button>
</div>", "modules/XC/ThemeTweaker/button/themetweaker-tab.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/button/themetweaker-tab.twig");
    }
}
