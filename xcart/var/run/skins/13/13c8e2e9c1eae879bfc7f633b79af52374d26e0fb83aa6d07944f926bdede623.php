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

/* modules/XC/Reviews/average_rating/rating.twig */
class __TwigTemplate_3f37e38aba78a7e9e0d1f4c08404e06c83d8b858ed1c224a11b97a3619614bc4 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isVisibleAverageRatingOnPage", [], "method")) {
            // line 6
            echo " <div class=\"rating";
            if ($this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method")) {
                echo " edit";
            }
            echo "\">
     ";
            // line 7
            if ($this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method")) {
                // line 8
                echo "         ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\FormField\\Input\\Rating", "fieldName" => "rating", "rate" => $this->getAttribute(($context["this"] ?? null), "getAverageRating", [], "method"), "is_editable" => $this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method"), "max" => "5"]]), "html", null, true);
                echo "
     ";
            }
            // line 10
            echo "     ";
            if ( !$this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method")) {
                // line 11
                echo "         ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\VoteBar", "rate" => $this->getAttribute(($context["this"] ?? null), "getAverageRating", [], "method"), "max" => "5"]]), "html", null, true);
                echo "
     ";
            }
            // line 13
            echo "
     ";
            // line 14
            if (("tab" != $this->getAttribute(($context["this"] ?? null), "place", []))) {
                // line 15
                echo "         <div class=\"rating-tooltip\">
             ";
                // line 16
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "reviews.tooltip.rating"]]), "html", null, true);
                echo "
         </div>
     ";
            }
            // line 19
            echo "
 </div>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/XC/Reviews/average_rating/rating.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 19,  67 => 16,  64 => 15,  62 => 14,  59 => 13,  53 => 11,  50 => 10,  44 => 8,  42 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Reviews/average_rating/rating.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/modules/XC/Reviews/average_rating/rating.twig");
    }
}
