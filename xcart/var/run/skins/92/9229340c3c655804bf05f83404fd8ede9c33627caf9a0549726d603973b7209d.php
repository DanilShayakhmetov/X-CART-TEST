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

/* modules/XC/Reviews/product/details/rating.twig */
class __TwigTemplate_4643563afd65e1ce45c12ecdf031dcc52b2379aebaa8d64accd04f7fd8fba7d3 extends \XLite\Core\Templating\Twig\Template
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
            // line 14
            if (($this->getAttribute(($context["this"] ?? null), "getWidgetMode", [], "method") == "product-details")) {
                // line 15
                echo "      <span class=\"average-rating\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAverageRating", [], "method"), "html", null, true);
                echo "</span>
    ";
            }
            // line 17
            echo "    
    ";
            // line 18
            if ($this->getAttribute(($context["this"] ?? null), "isVisibleReviewsCount", [], "method")) {
                // line 19
                echo "      <span class=\"separator\">|</span>
      <div class=\"reviews-count no-reviews\">
        ";
                // line 21
                if (($this->getAttribute(($context["this"] ?? null), "getWidgetMode", [], "method") == "product-details")) {
                    // line 22
                    echo "          <span>
            <a href=\"";
                    // line 23
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatedProductURL", [], "method"), "html", null, true);
                    echo "\" class=\"link-to-tab\">
              ";
                    // line 24
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Reviews: X", ["count" => $this->getAttribute(($context["this"] ?? null), "getReviewsCount", [], "method")]]), "html", null, true);
                    echo "
            </a>
          </span>
        ";
                } else {
                    // line 28
                    echo "          &mdash;
          <a href=\"";
                    // line 29
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatedProductURL", [], "method"), "html", null, true);
                    echo "\" class=\"link-to-tab\">
            ";
                    // line 30
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getReviewsCount", [], "method"), "html", null, true);
                    echo "
          </a>
        
        ";
                }
                // line 34
                echo "      </div>
    ";
            }
            // line 36
            echo "    ";
            if ($this->getAttribute(($context["this"] ?? null), "isVisibleAddReviewLink", [0 => $this->getAttribute(($context["this"] ?? null), "product", [])], "method")) {
                // line 37
                echo "      <span class=\"separator\">|</span>
      ";
                // line 38
                if ($this->getAttribute(($context["this"] ?? null), "isReplaceAddReviewWithLogin", [], "method")) {
                    // line 39
                    echo "        ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\Button\\PopupLoginLink", "label" => $this->getAttribute(($context["this"] ?? null), "getReviewsLinkLabel", [], "method"), "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
                    echo "
      ";
                } else {
                    // line 41
                    echo "        <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatedProductURL", [], "method"), "html", null, true);
                    echo "\" class=\"link-to-tab\">
          ";
                    // line 42
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\Button\\Customer\\AddReviewLink", "label" => $this->getAttribute(($context["this"] ?? null), "getReviewsLinkLabel", [], "method"), "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
                    echo "
        </a>
      ";
                }
                // line 45
                echo "    ";
            }
            // line 46
            echo "  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/XC/Reviews/product/details/rating.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  128 => 46,  125 => 45,  119 => 42,  114 => 41,  108 => 39,  106 => 38,  103 => 37,  100 => 36,  96 => 34,  89 => 30,  85 => 29,  82 => 28,  75 => 24,  71 => 23,  68 => 22,  66 => 21,  62 => 19,  60 => 18,  57 => 17,  51 => 15,  49 => 14,  44 => 12,  40 => 11,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Reviews/product/details/rating.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/modules/XC/Reviews/product/details/rating.twig");
    }
}
