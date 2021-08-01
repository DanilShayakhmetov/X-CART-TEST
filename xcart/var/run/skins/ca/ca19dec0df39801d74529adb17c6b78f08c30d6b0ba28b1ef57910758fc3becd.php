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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Coupons/coupons_minilist.twig */
class __TwigTemplate_5103e11bda2384f112fe1dac2240c191cfb34a2eeccba5feb30f37eb45fa528e extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isDiscountCouponSubpanelVisible", [0 => $this->getAttribute(($context["this"] ?? null), "surcharge", [])], "method")) {
            // line 8
            echo "  <ul class=\"discount-coupons-panel\">
    ";
            // line 9
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getDiscountCoupons", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["usedCoupon"]) {
                // line 10
                echo "      <li title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => $this->getAttribute($context["usedCoupon"], "getValue", [], "method"), 1 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getCurrency", [], "method")], "method"), "html", null, true);
                echo "\">
        ";
                // line 11
                if ($this->getAttribute($context["usedCoupon"], "isDeleted", [], "method")) {
                    // line 12
                    echo "          ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["usedCoupon"], "getCode", [], "method"), "html", null, true);
                    echo "
        ";
                } else {
                    // line 14
                    echo "          <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "buildUrl", [0 => "coupon", 1 => "", 2 => ["id" => $this->getAttribute($this->getAttribute($context["usedCoupon"], "coupon", []), "getId", [], "method")]], "method"), "html", null, true);
                    echo "\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["usedCoupon"], "getPublicName", [], "method"), "html", null, true);
                    echo "</a>
        ";
                }
                // line 16
                echo "      </li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['usedCoupon'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 18
            echo "  </ul>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Coupons/coupons_minilist.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  70 => 18,  63 => 16,  55 => 14,  49 => 12,  47 => 11,  42 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Coupons/coupons_minilist.twig", "");
    }
}
