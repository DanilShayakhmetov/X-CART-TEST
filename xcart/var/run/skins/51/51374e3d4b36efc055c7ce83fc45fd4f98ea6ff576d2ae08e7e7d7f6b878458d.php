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

/* vote_bar/vote_bar.twig */
class __TwigTemplate_f7905d6600bd4115c2dc33273b0b4e9879a7e24bebce3751e8a1b75c50cf6c49 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"vote-bar";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "isEditable", [], "method")) {
            echo " editable";
        }
        echo "\">

  <div class=\"rating-stars\">
    <div class=\"stars-row\">
      ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getStarsCount", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["num"]) {
            // line 10
            echo "        <div class=\"star-single\"><span class=\"fa fa-star\"></span></div>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['num'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 12
        echo "    </div>

    <div class=\"stars-row full\" style=\"width: ";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPercent", [], "method"), "html", null, true);
        echo "%;\">
      ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getStarsCount", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["num"]) {
            // line 16
            echo "        <div class=\"star-single\"><span class=\"fa fa-star\"></span></div>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['num'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        echo "    </div>

    ";
        // line 20
        if ($this->getAttribute(($context["this"] ?? null), "isEditable", [], "method")) {
            // line 21
            echo "    <div class=\"stars-row hovered\" style=\"display: none;\">
      ";
            // line 22
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getStarsCount", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["num"]) {
                // line 23
                echo "        <div class=\"star-single star-num-";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["num"], "html", null, true);
                echo "\" data-rating=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["num"], "html", null, true);
                echo "\" data-rating-label=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatingLabelFor", [0 => $context["num"]], "method"), "html", null, true);
                echo "\"><span class=\"fa fa-star\"></span></div>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['num'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 25
            echo "    </div>

    <input type=\"hidden\" name=\"";
            // line 27
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFieldName", [], "method"), "html", null, true);
            echo "\" value=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRating", [], "method"), "html", null, true);
            echo "\" />
    ";
        }
        // line 29
        echo "  </div>

  ";
        // line 31
        if ($this->getAttribute(($context["this"] ?? null), "getRatingLabel", [], "method")) {
            // line 32
            echo "    <span class=\"rating-label\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getRatingLabel", [], "method"), "html", null, true);
            echo "</span>
  ";
        }
        // line 34
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "vote_bar/vote_bar.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 34,  115 => 32,  113 => 31,  109 => 29,  102 => 27,  98 => 25,  85 => 23,  81 => 22,  78 => 21,  76 => 20,  72 => 18,  65 => 16,  61 => 15,  57 => 14,  53 => 12,  46 => 10,  42 => 9,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "vote_bar/vote_bar.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/common/vote_bar/vote_bar.twig");
    }
}
