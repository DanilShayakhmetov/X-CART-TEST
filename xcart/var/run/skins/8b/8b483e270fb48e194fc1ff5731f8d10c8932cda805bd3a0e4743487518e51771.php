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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/average_rating/details.twig */
class __TwigTemplate_f1a962db475d010a3f0c3dbcb3ed90d979117020252f625c48481bea3a78ac2f extends \XLite\Core\Templating\Twig\Template
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
        // line 7
        echo "
";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "isVisibleAverageRating", [], "method")) {
            // line 9
            echo "  <div class=\"ratings-details\">
    <div class=\"title\">
      ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Rating of votes"]), "html", null, true);
            echo " (";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getVotesCount", [], "method"), "html", null, true);
            echo ")
    </div>
    <table>
      ";
            // line 14
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getRatings", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["rating"]) {
                // line 15
                echo "        <tr class=\"rating-";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["rating"], "rating", []), "html", null, true);
                echo "\">
          <td class=\"indent\"></td>
          <td class=\"rating-digit\">";
                // line 17
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["rating"], "rating", []), "html", null, true);
                echo "</td>
          <td class=\"rating\">";
                // line 18
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\Reviews\\View\\VoteBar", "rate" => "1", "max" => "1", "length" => "1"]]), "html", null, true);
                echo "</td>
          <td class=\"percent\">
            <div class=\"rating-line rating-";
                // line 20
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["rating"], "rating", []), "html", null, true);
                echo "\" style=\"width:";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["rating"], "percent", []), "html", null, true);
                echo "%\">&nbsp;</div>
            ";
                // line 21
                if ($this->getAttribute($context["rating"], "showPercentLastDiv", [])) {
                    echo "<div class=\"rating-end\">&nbsp;</div>";
                }
                // line 22
                echo "          </td>
          <td class=\"count count-";
                // line 23
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["rating"], "count", []), "html", null, true);
                echo "\"><span class=\"count-number\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["rating"], "count", []), "html", null, true);
                echo "</span><span class=\"count-text\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["customers"]), "html", null, true);
                echo "</span></td>
        </tr>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rating'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 26
            echo "      <tr><td colspan=\"5\">&nbsp;</td></tr>
    </table>
  
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/average_rating/details.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 26,  79 => 23,  76 => 22,  72 => 21,  66 => 20,  61 => 18,  57 => 17,  51 => 15,  47 => 14,  39 => 11,  35 => 9,  33 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/Reviews/average_rating/details.twig", "");
    }
}
