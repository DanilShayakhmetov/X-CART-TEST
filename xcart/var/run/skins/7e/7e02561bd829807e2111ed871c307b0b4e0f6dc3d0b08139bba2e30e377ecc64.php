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

/* form_field/input/checkbox/on_off.twig */
class __TwigTemplate_48074a231d82ca1b8d6dae67f4d261b49422e95a2f0a063ccde60ef90f196a9b extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCssClass", [], "method"), "html", null, true);
        echo "\">
  ";
        // line 5
        if ( !$this->getAttribute(($context["this"] ?? null), "isDisabled", [], "method")) {
            // line 6
            echo "    <input type=\"hidden\" name=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "\" value=\"\" />
  ";
        }
        // line 8
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isDisabled", [], "method")) {
            // line 9
            echo "    <input type=\"hidden\" name=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "\" value=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getValue", [], "method"), "html", null, true);
            echo "\" />
  ";
        }
        // line 11
        echo "  <input";
        echo $this->getAttribute(($context["this"] ?? null), "getAttributesCode", [], "method");
        echo " />
  <label for=\"";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFieldId", [], "method"), "html", null, true);
        echo "\">
    <div class=\"onoffswitch-inner\">
      <div class=\"on-caption\">";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getOnLabel", [], "method")]), "html", null, true);
        echo "</div>
      <div class=\"off-caption\">";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getOffLabel", [], "method")]), "html", null, true);
        echo "</div>
    </div>
    <span class=\"onoffswitch-switch\"></span>
  </label>
</div>
";
    }

    public function getTemplateName()
    {
        return "form_field/input/checkbox/on_off.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 15,  64 => 14,  59 => 12,  54 => 11,  46 => 9,  43 => 8,  37 => 6,  35 => 5,  30 => 4,);
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
 # OnOff checkbox
 #}
<div class=\"{{ this.getCssClass() }}\">
  {% if not this.isDisabled() %}
    <input type=\"hidden\" name=\"{{ this.getName() }}\" value=\"\" />
  {% endif %}
  {% if this.isDisabled() %}
    <input type=\"hidden\" name=\"{{ this.getName() }}\" value=\"{{ this.getValue() }}\" />
  {% endif %}
  <input{{ this.getAttributesCode()|raw }} />
  <label for=\"{{ this.getFieldId() }}\">
    <div class=\"onoffswitch-inner\">
      <div class=\"on-caption\">{{ t(this.getOnLabel()) }}</div>
      <div class=\"off-caption\">{{ t(this.getOffLabel()) }}</div>
    </div>
    <span class=\"onoffswitch-switch\"></span>
  </label>
</div>
", "form_field/input/checkbox/on_off.twig", "/mff/xcart/skins/customer/form_field/input/checkbox/on_off.twig");
    }
}
