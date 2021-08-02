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

/* modules/CDev/Coupons/cart_coupons.twig */
class __TwigTemplate_67beb088768f51f1ac2722bff7a7b8264341664a00d8d26a48f3620bfbc0851a extends \XLite\Core\Templating\Twig\Template
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
<div class=\"coupons clearfix ";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "showCouponBoxAlways", [], "method")) {
            echo "opened";
        }
        echo "\">
  ";
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "isCouponPanelVisible", [], "method")) {
            // line 7
            echo "    ";
            if (( !$this->getAttribute(($context["this"] ?? null), "getCoupons", [], "method") &&  !$this->getAttribute(($context["this"] ?? null), "showCouponBoxAlways", [], "method"))) {
                // line 8
                echo "      <div class=\"new more\"><a href=\"#\"><span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Have a discount coupon?"]), "html", null, true);
                echo "</span></a></div>
    ";
            }
            // line 10
            echo "    ";
            if (($this->getAttribute(($context["this"] ?? null), "getCoupons", [], "method") &&  !$this->getAttribute(($context["this"] ?? null), "showCouponBoxAlways", [], "method"))) {
                // line 11
                echo "      <div class=\"new add\"><a href=\"#\"><span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Have more coupons?"]), "html", null, true);
                echo "</span></a></div>
    ";
            }
            // line 13
            echo "
    <div class=\"add-coupon clearfix\"
      ";
            // line 15
            if ( !$this->getAttribute(($context["this"] ?? null), "showCouponBoxAlways", [], "method")) {
                // line 16
                echo "        style=\"display: none;\"
      ";
            } else {
                // line 18
                echo "        style=\"display: block;\"
      ";
            }
            // line 20
            echo "    >
      ";
            // line 21
            $this->startForm("\\XLite\\Module\\CDev\\Coupons\\View\\Form\\Customer\\AddCoupon");            // line 22
            echo "        ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "fieldName" => "code", "required" => "true", "placeholder" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Enter code", [], null, "placeholder"]), "maxlength" => "16", "fieldOnly" => $this->getAttribute(($context["this"] ?? null), "isFieldOnly", [], "method"), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Coupon code"])]]), "html", null, true);
            echo "
        ";
            // line 23
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\CDev\\Coupons\\View\\Button\\AddCoupon"]]), "html", null, true);
            echo "
      ";
            $this->endForm();            // line 25
            echo "    </div>

  ";
        }
        // line 28
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "modules/CDev/Coupons/cart_coupons.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  91 => 28,  86 => 25,  82 => 23,  77 => 22,  76 => 21,  73 => 20,  69 => 18,  65 => 16,  63 => 15,  59 => 13,  53 => 11,  50 => 10,  44 => 8,  41 => 7,  39 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/CDev/Coupons/cart_coupons.twig", "/mff/xcart/skins/customer/modules/CDev/Coupons/cart_coupons.twig");
    }
}
