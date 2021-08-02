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

/* product/top_sellers/body.twig */
class __TwigTemplate_e0b2c9ae0fb0d8acf26ea3b929bb1832f94bc9c3d0a20b581496dee0dd0ca7a1 extends \XLite\Core\Templating\Twig\Template
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
";
        // line 5
        if ( !$this->getAttribute(($context["this"] ?? null), "isEmptyStats", [], "method")) {
            // line 6
            echo "  <div class=\"top-sellers\">
    <h3>";
            // line 7
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Bestsellers"]), "html", null, true);
            echo "</h3>
    <div id=\"period-";
            // line 8
            echo $this->getAttribute(($context["this"] ?? null), "getSelectedPeriod", [], "method");
            echo "\" class=\"block-container\">
      ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\ItemsList\\Model\\Product\\Admin\\TopSellers", "period" => $this->getAttribute(($context["this"] ?? null), "getSelectedPeriod", [], "method"), "products_limit" => "5", "availability" => $this->getAttribute(($context["this"] ?? null), "getSelectedAvailability", [], "method")]]), "html", null, true);
            echo "
    </div>

    <div class=\"selectors\">
      ";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Select\\Regular", "label" => "For the period", "options" => $this->getAttribute(($context["this"] ?? null), "getOptions", [], "method"), "fieldName" => "period", "value" => $this->getAttribute(($context["this"] ?? null), "getSelectedPeriod", [], "method")]]), "html", null, true);
            echo "

      ";
            // line 15
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Select\\Regular", "label" => "Products", "options" => $this->getAttribute(($context["this"] ?? null), "getAvailabilityOptions", [], "method"), "fieldName" => "availability", "value" => $this->getAttribute(($context["this"] ?? null), "getSelectedAvailability", [], "method")]]), "html", null, true);
            echo "
    </div>
  </div>
";
        } else {
            // line 19
            echo "  <div class=\"empty-list\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["No products sold yet"]), "html", null, true);
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "product/top_sellers/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 19,  58 => 15,  53 => 13,  46 => 9,  42 => 8,  38 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "product/top_sellers/body.twig", "/mff/xcart/skins/admin/product/top_sellers/body.twig");
    }
}
