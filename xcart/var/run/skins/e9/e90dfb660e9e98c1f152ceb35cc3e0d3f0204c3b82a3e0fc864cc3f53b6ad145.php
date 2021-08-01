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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/reviews_page/parts/page.product.twig */
class __TwigTemplate_35ce3d19a7d8b6f8667a3c6e28b159fa18f79fc387394a255f9ea3cbbb48156e extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "product", [])) {
            // line 8
            echo "  <div class=\"product-details product-reviews-details clearfix\">
    <div class=\"image";
            // line 9
            if ( !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "hasImage", [], "method")) {
                echo " empty";
            }
            echo "\" style=\"width: ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getMaxImageWidth", [], "method"), "html", null, true);
            echo "px;\">
      ";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Product\\Details\\Customer\\Image", "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
            echo "
    </div>
    <div class=\"product-details-info\">
      <h1 class=\"fn title\">";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method"), "html", null, true);
            echo "</h1>
      <a href=\"";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "product", "", ["product_id" => $this->getAttribute(($context["this"] ?? null), "getProductId", [], "method"), "category_id" => $this->getAttribute(($context["this"] ?? null), "getCategoryId", [], "method")]]), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Back to product details"]), "html", null, true);
            echo "</a>
  
      <div>
        ";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\Reviews\\View\\Customer\\ReviewsPage\\AverageRating", "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
            echo "
      </div>
    </div>
  
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/reviews_page/parts/page.product.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 17,  56 => 14,  52 => 13,  46 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/reviews_page/parts/page.product.twig", "");
    }
}
