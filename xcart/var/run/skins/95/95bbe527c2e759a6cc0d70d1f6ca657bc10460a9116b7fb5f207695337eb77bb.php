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

/* /mff/xcart/skins/admin/modules/CDev/Sale/category/sale_label.twig */
class __TwigTemplate_3ee779cfc9ed6d8415d112ea51e9135de91c7721ca9ca315b222692dfa325901 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"sale-discount-labels\">
  ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getApplicableSaleDiscounts", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["saleDiscount"]) {
            // line 9
            echo "    ";
            if ($this->getAttribute(($context["this"] ?? null), "getSaleDiscountEditLink", [0 => $context["saleDiscount"]], "method")) {
                // line 10
                echo "      <a class=\"sale-label-link\" href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSaleDiscountEditLink", [0 => $context["saleDiscount"]], "method"), "html", null, true);
                echo "\">
        <span class=\"product-name-sale-label\">";
                // line 11
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["saleDiscount"], "getName", [], "method"), "html", null, true);
                echo "</span>
      </a>
    ";
            } else {
                // line 14
                echo "      <span class=\"product-name-sale-label\">
        ";
                // line 15
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["saleDiscount"], "getName", [], "method"), "html", null, true);
                echo "
      </span>
    ";
            }
            // line 18
            echo "  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['saleDiscount'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/CDev/Sale/category/sale_label.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  67 => 19,  61 => 18,  55 => 15,  52 => 14,  46 => 11,  41 => 10,  38 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/CDev/Sale/category/sale_label.twig", "");
    }
}
