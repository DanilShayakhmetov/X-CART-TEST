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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/product/details/rating.twig */
class __TwigTemplate_8d679c3b94daa41b1fbceec3b29eca8948586791974db3a6e31ca57c266020d2 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isVisibleAverageRatingOnPage", [], "method")) {
            // line 8
            echo "  <div class=\"product-average-rating\">
    <input type=\"hidden\" name=\"target_widget\"
           value=\"\\XLite\\Module\\XC\\Reviews\\View\\Customer\\ProductInfo\\Details\\AverageRating\"/>
    <input type=\"hidden\" name=\"widgetMode\" value=\"";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getWidgetMode", [], "method"), "html", null, true);
            echo "\"/>
    ";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "reviews.product.rating", "product" => $this->getAttribute(($context["this"] ?? null), "getRatedProduct", [], "method")]]), "html", null, true);
            echo "
    ";
            // line 13
            if ($this->getAttribute(($context["this"] ?? null), "isVisibleReviewsCount", [], "method")) {
                // line 14
                echo "      <div class=\"reviews-count no-reviews\">
        &mdash;
        <a href=\"";
                // line 16
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatedProductURL", [], "method"), "html", null, true);
                echo "\" class=\"link-to-tab\">
          ";
                // line 17
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getReviewsCount", [], "method"), "html", null, true);
                echo "
        </a>
      </div>
    ";
            }
            // line 21
            echo "    ";
            if ($this->getAttribute(($context["this"] ?? null), "isVisibleAddReviewLink", [0 => $this->getAttribute(($context["this"] ?? null), "product", [])], "method")) {
                // line 22
                echo "        <span class=\"separator\">|</span>
      ";
                // line 23
                if ($this->getAttribute(($context["this"] ?? null), "isReplaceAddReviewWithLogin", [], "method")) {
                    // line 24
                    echo "        ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\Button\\PopupLoginLink", "label" => $this->getAttribute(($context["this"] ?? null), "getReviewsLinkLabel", [], "method"), "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
                    echo "
      ";
                } else {
                    // line 26
                    echo "        <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatedProductURL", [], "method"), "html", null, true);
                    echo "\" class=\"link-to-tab\">
          ";
                    // line 27
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\Button\\Customer\\AddReviewLink", "label" => $this->getAttribute(($context["this"] ?? null), "getReviewsLinkLabel", [], "method"), "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
                    echo "
        </a>
      ";
                }
                // line 30
                echo "    ";
            }
            // line 31
            echo "  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/product/details/rating.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 31,  90 => 30,  84 => 27,  79 => 26,  73 => 24,  71 => 23,  68 => 22,  65 => 21,  58 => 17,  54 => 16,  50 => 14,  48 => 13,  44 => 12,  40 => 11,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/product/details/rating.twig", "");
    }
}
