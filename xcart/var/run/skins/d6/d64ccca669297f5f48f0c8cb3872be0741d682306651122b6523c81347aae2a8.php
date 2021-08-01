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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/product/sale_label.twig */
class __TwigTemplate_50e59e5fa3c05c6938ed602a548fde1170e05f2c6843a10f0d8d8657b305e5ef extends \XLite\Core\Templating\Twig\Template
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
        // line 7
        echo "
<div class=\"sale-discount-labels\">
  ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "participateSaleAdmin", [0 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method")) {
            // line 10
            echo "    <span
      id=\"product-sale-label-";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getProductId", [], "method"), "html", null, true);
            echo "\"
      class=\"product-name-sale-label\">
      ";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Sale"]), "html", null, true);
            echo "
    </span>
  ";
        } else {
            // line 16
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getApplicableSaleDiscounts", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["saleDiscount"]) {
                // line 17
                echo "      ";
                if ($this->getAttribute(($context["this"] ?? null), "getSaleDiscountEditLink", [0 => $context["saleDiscount"]], "method")) {
                    // line 18
                    echo "        <a class=\"sale-label-link\" href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSaleDiscountEditLink", [0 => $context["saleDiscount"]], "method"), "html", null, true);
                    echo "\">
          <span class=\"product-name-sale-label\">";
                    // line 19
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["saleDiscount"], "getName", [], "method"), "html", null, true);
                    echo "</span>
        </a>
      ";
                } else {
                    // line 22
                    echo "        <span class=\"product-name-sale-label\">
          ";
                    // line 23
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["saleDiscount"], "getName", [], "method"), "html", null, true);
                    echo "
        </span>
      ";
                }
                // line 26
                echo "    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['saleDiscount'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 27
            echo "  ";
        }
        // line 28
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/product/sale_label.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 28,  84 => 27,  78 => 26,  72 => 23,  69 => 22,  63 => 19,  58 => 18,  55 => 17,  50 => 16,  44 => 13,  39 => 11,  36 => 10,  34 => 9,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/product/sale_label.twig", "");
    }
}
