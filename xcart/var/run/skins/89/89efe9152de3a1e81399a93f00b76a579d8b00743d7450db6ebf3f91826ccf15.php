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

/* button/popup_button.twig */
class __TwigTemplate_b39b4f19a4e323e882c03e870c1b6ac60e348aa8d631a85c6639699fb3b26ffc extends \XLite\Core\Templating\Twig\Template
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
<div class=\"button-container\">
  <button type=\"button\" class=\"";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getClass", [], "method"), "html", null, true);
        echo "\">
    ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getURLParams", [], "method")], "method"), "html", null, true);
        echo "
    ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "icon-style"], "method")) {
            // line 9
            echo "      <i class=\"button-icon ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "icon-style"], "method"), "html", null, true);
            echo "\"></i>
    ";
        }
        // line 11
        echo "    <span>";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getButtonContent", [], "method")]), "html", null, true);
        echo "</span>
  </button>
</div>
";
    }

    public function getTemplateName()
    {
        return "button/popup_button.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 11,  44 => 9,  42 => 8,  38 => 7,  34 => 6,  30 => 4,);
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
 # Popup button
 #}

<div class=\"button-container\">
  <button type=\"button\" class=\"{{ this.getClass() }}\">
    {{ this.displayCommentedData(this.getURLParams()) }}
    {% if this.getParam('icon-style') %}
      <i class=\"button-icon {{ this.getParam('icon-style') }}\"></i>
    {% endif %}
    <span>{{ t(this.getButtonContent()) }}</span>
  </button>
</div>
", "button/popup_button.twig", "/mff/xcart/skins/admin/button/popup_button.twig");
    }
}
