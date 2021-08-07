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

/* modules/XC/ProductComparison/add_to_cart/body.twig */
class __TwigTemplate_a91a65072f07527a4a19ee6cf22b6f4a850f77fdb2c77f03fdf7ab991a1c1ad3 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "isOutOfStock", [], "method")) {
            // line 6
            echo "<span class=\"out-of-stock\">
  ";
            // line 7
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Out of stock"]), "html", null, true);
            echo "
</span>
";
        } else {
            // line 10
            echo "  ";
            $this->startForm("\\XLite\\View\\Form\\Product\\AddToCart", ["product" => $this->getAttribute(($context["this"] ?? null), "product", [])]);            // line 11
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Submit", "label" => "Add to cart", "style" => "regular-main-button add2cart"]]), "html", null, true);
            echo "
";
            $this->endForm();        }
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/add_to_cart/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 11,  44 => 10,  38 => 7,  35 => 6,  33 => 5,  30 => 4,);
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
 # Add to cart
 #}

{% if this.product.isOutOfStock() %}
<span class=\"out-of-stock\">
  {{ t('Out of stock') }}
</span>
{% else %}
  {% form '\\\\XLite\\\\View\\\\Form\\\\Product\\\\AddToCart' with {product: this.product} %}
  {{ widget('\\\\XLite\\\\View\\\\Button\\\\Submit', label='Add to cart', style='regular-main-button add2cart') }}
{% endform %}
{% endif %}
", "modules/XC/ProductComparison/add_to_cart/body.twig", "/mff/xcart/skins/customer/modules/XC/ProductComparison/add_to_cart/body.twig");
    }
}
