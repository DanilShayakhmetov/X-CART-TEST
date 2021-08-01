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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/CDev/Coupons/coupons_minilist.twig */
class __TwigTemplate_2f876df87f14c52567f241f31cea407e55dddb3a107f63b2c3a33c9e13ab4bb6 extends \XLite\Core\Templating\Twig\Template
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
        // line 8
        echo "
";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isDiscountCouponSubpanelVisible", [0 => $this->getAttribute(($context["this"] ?? null), "surcharge", [])], "method")) {
            // line 10
            echo "  <ul class=\"discount-coupons-panel\">
    ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getDiscountCoupons", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["usedCoupon"]) {
                // line 12
                echo "      <li>
        <span>";
                // line 13
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["usedCoupon"], "getPublicName", [], "method"), "html", null, true);
                echo "</span>
        ";
                // line 14
                if ($this->getAttribute(($context["this"] ?? null), "isDiscountCouponRemoveVisible", [], "method")) {
                    // line 15
                    echo "          <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "buildUrl", [0 => "coupon", 1 => "remove", 2 => ["id" => $this->getAttribute($context["usedCoupon"], "getId", [], "method")]], "method"), "html", null, true);
                    echo "\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Remove"]), "html", null, true);
                    echo "</a>
        ";
                }
                // line 17
                echo "      </li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['usedCoupon'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            echo "  </ul>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/CDev/Coupons/coupons_minilist.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 19,  59 => 17,  51 => 15,  49 => 14,  45 => 13,  42 => 12,  38 => 11,  35 => 10,  33 => 9,  30 => 8,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/CDev/Coupons/coupons_minilist.twig", "");
    }
}
