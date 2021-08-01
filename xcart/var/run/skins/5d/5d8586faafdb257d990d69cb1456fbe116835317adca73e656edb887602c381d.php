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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/sale_discount_types/parts/percent.twig */
class __TwigTemplate_934e453ed7df315cebbf308bf4f6f18e4ad4b5ba2ddbd5b2abfa3e59db74bfdb extends \XLite\Core\Templating\Twig\Template
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
 <ul class=\"sale-discount sale-discount-percent\">
   <li class=\"discount-type\">
     <input
         id=\"sale-price-percent-off\"
         type=\"radio\"
         name=\"";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getNamePostedData", [0 => "discountType"], "method"), "html", null, true);
        echo "\"
         value=\"";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, twig_constant("XLite\\Model\\Product::SALE_DISCOUNT_TYPE_PERCENT"), "html", null, true);
        echo "\"
         ";
        // line 14
        if (($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "discountType"], "method") == twig_constant("XLite\\Model\\Product::SALE_DISCOUNT_TYPE_PERCENT"))) {
            echo "checked=\"checked\"";
        }
        echo " />
     <label for=\"sale-price-percent-off\">
      ";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Percent off"]), "html", null, true);
        echo "
     </label>
   </li>
   <li class=\"sale-price-value\">
     ";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\Text\\Symbol", "fieldOnly" => "true", "mouseWheelIcon" => "false", "symbol" => "%", "fieldId" => ("sale-price-value-" . twig_constant("XLite\\Model\\Product::SALE_DISCOUNT_TYPE_PERCENT")), "value" => $this->getAttribute(($context["this"] ?? null), "getPercentOffValue", [], "method")]]), "html", null, true);
        echo "
   </li>
 </ul>

 <div class=\"clear\"></div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/sale_discount_types/parts/percent.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  60 => 20,  53 => 16,  46 => 14,  42 => 13,  38 => 12,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/sale_discount_types/parts/percent.twig", "");
    }
}
