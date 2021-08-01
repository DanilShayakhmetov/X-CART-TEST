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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/average_rating/rating.twig */
class __TwigTemplate_d1b02f88d3bfc36c5750042aaa6f145bcdfbd2d4ee9507ff8c819705a9756747 extends \XLite\Core\Templating\Twig\Template
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
            echo "  <div class=\"rating";
            if ($this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method")) {
                echo " edit";
            }
            echo "\">
    ";
            // line 9
            if ($this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method")) {
                // line 10
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\FormField\\Input\\Rating", "fieldName" => "rating", "rate" => $this->getAttribute(($context["this"] ?? null), "getAverageRating", [], "method"), "is_editable" => $this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method"), "max" => "5"]]), "html", null, true);
                echo "
    ";
            }
            // line 12
            echo "    ";
            if ( !$this->getAttribute(($context["this"] ?? null), "isAllowedRateProduct", [], "method")) {
                // line 13
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\VoteBar", "rate" => $this->getAttribute(($context["this"] ?? null), "getAverageRating", [], "method"), "max" => "5"]]), "html", null, true);
                echo "
    ";
            }
            // line 15
            echo "    <br />
  
    ";
            // line 17
            if (("tab" != $this->getAttribute(($context["this"] ?? null), "place", []))) {
                // line 18
                echo "      <div class=\"rating-tooltip\">
        ";
                // line 19
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "reviews.tooltip.rating"]]), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 22
            echo "  
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/average_rating/rating.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 22,  68 => 19,  65 => 18,  63 => 17,  59 => 15,  53 => 13,  50 => 12,  44 => 10,  42 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/average_rating/rating.twig", "");
    }
}
