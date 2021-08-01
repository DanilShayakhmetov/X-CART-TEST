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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/sale_discount_types/parts/price.twig */
class __TwigTemplate_780f690bffd87ebbc554ea80974968dbf0e1a8a456c9a64fb1bbb41c5c8e4b73 extends \XLite\Core\Templating\Twig\Template
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
 <ul class=\"sale-discount sale-discount-price\">
   <li class=\"discount-type\">
     <input
         id=\"sale-price-sale-price\"
         type=\"radio\"
         name=\"";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getNamePostedData", [0 => "discountType"], "method"), "html", null, true);
        echo "\"
         value=\"";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, twig_constant("XLite\\Model\\Product::SALE_DISCOUNT_TYPE_PRICE"), "html", null, true);
        echo "\"
         ";
        // line 14
        if (($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "discountType"], "method") == twig_constant("XLite\\Model\\Product::SALE_DISCOUNT_TYPE_PRICE"))) {
            echo "checked=\"checked\"";
        }
        echo " />
     <label for=\"sale-price-sale-price\">
      ";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Sale price"]), "html", null, true);
        echo "
     </label>
   </li>
   <li class=\"sale-price-value\">
     ";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\Text\\Price", "fieldOnly" => "true", "mouseWheelIcon" => "false", "fieldId" => ("sale-price-value-" . twig_constant("XLite\\Model\\Product::SALE_DISCOUNT_TYPE_PRICE")), "value" => $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "salePriceValue"], "method")]]), "html", null, true);
        echo "
   </li>
 </ul>

 <div class=\"clear\"></div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/sale_discount_types/parts/price.twig";
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
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Sale/sale_discount_types/parts/price.twig", "");
    }
}
