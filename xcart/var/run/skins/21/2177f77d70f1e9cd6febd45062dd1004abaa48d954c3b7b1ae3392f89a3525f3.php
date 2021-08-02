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

/* order/statistics/informer/parts/day.twig */
class __TwigTemplate_6afc4a25ff49eb009f741e0f2394151f8d62c72aac1d8506b7f904c1cc7d73cd extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"order-statistics ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["tab"] ?? null), "class", []), "html", null, true);
        echo "\">
  <div class=\"revenue ";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRevenueClass", [0 => ($context["tab"] ?? null)], "method"), "html", null, true);
        echo "\">
    <span class=\"label\">";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Revenue"]), "html", null, true);
        echo ":</span>
    <span class=\"value\">";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => $this->getAttribute($this->getAttribute(($context["tab"] ?? null), "revenue", []), "value", [])], "method"), "html", null, true);
        echo "</span>
  </div>
  <div class=\"dynamic-icon ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRevenueClass", [0 => ($context["tab"] ?? null)], "method"), "html", null, true);
        echo "\">";
        echo $this->getAttribute(($context["this"] ?? null), "getIcon", [0 => ($context["tab"] ?? null)], "method");
        echo "</div>
  <div class=\"orders\">
    <span class=\"label\">";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Orders"]), "html", null, true);
        echo ":</span>
    <span class=\"value\">";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["tab"] ?? null), "orders", []), "value", []), "html", null, true);
        echo "</span>
  </div>
  ";
        // line 14
        if ($this->getAttribute(($context["this"] ?? null), "showPrevious", [0 => ($context["tab"] ?? null)], "method")) {
            // line 15
            echo "    <div class=\"previous\">
      <span class=\"label\">";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Yesterday"]), "html", null, true);
            echo "</span>
      <span class=\"value\">";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => $this->getAttribute($this->getAttribute(($context["tab"] ?? null), "revenue", []), "prev", [])], "method"), "html", null, true);
            echo "</span>
      <span class=\"separator\">/</span>
      <span class=\"value\">";
            // line 19
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["tab"] ?? null), "orders", []), "prev", []), "html", null, true);
            echo "</span>
    </div>
  ";
        }
        // line 22
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "order/statistics/informer/parts/day.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  84 => 22,  78 => 19,  73 => 17,  69 => 16,  66 => 15,  64 => 14,  59 => 12,  55 => 11,  48 => 9,  43 => 7,  39 => 6,  35 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "order/statistics/informer/parts/day.twig", "/mff/xcart/skins/admin/order/statistics/informer/parts/day.twig");
    }
}
