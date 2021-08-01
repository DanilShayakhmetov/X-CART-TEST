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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/checkout/steps/review/parts/items.modifiers.twig */
class __TwigTemplate_855911f11f9cbb51eaf0334b4d77ab544cb3b1727ffe70aa5ec3fd837ce5a1f4 extends \XLite\Core\Templating\Twig\Template
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
<ul class=\"modifiers\">
  ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getSurchargeTotals", [], "method"));
        foreach ($context['_seq'] as $context["sType"] => $context["surcharge"]) {
            // line 9
            echo "    <li class=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSurchargeClassName", [0 => $context["sType"], 1 => $context["surcharge"]], "method"), "html", null, true);
            echo "\">
      ";
            // line 10
            if (($this->getAttribute($context["surcharge"], "count", []) == "1")) {
                // line 11
                echo "        <span class=\"name\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["surcharge"], "lastName", []), "html", null, true);
                echo ":</span>
      ";
            } else {
                // line 13
                echo "        <span class=\"name list-owner\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["surcharge"], "name", []), "html", null, true);
                echo ":</span>
      ";
            }
            // line 15
            echo "      ";
            if ($this->getAttribute($context["surcharge"], "available", [])) {
                // line 16
                echo "        <span class=\"value\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Surcharge", "surcharge" => $this->getAttribute(($context["this"] ?? null), "formatSurcharge", [0 => $context["surcharge"]], "method"), "currency" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getCurrency", [], "method")]]), "html", null, true);
                echo "</span>
        <div class=\"surcharge-info-wrapper\">
            ";
                // line 19
                echo "            ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "checkout.review.surcharge.info", "surcharge" => $context["surcharge"]]]), "html", null, true);
                echo "
        </div>
      ";
            } else {
                // line 22
                echo "        <span class=\"value\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["n/a"]), "html", null, true);
                echo "</span>
      ";
            }
            // line 24
            echo "      ";
            if (($this->getAttribute($context["surcharge"], "count", []) == "1")) {
                // line 25
                echo "        ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "modifier", "type" => "nested", "surcharge" => $context["surcharge"], "sType" => $context["sType"], "cart" => $this->getAttribute(($context["this"] ?? null), "cart", [])]]), "html", null, true);
                echo "
      ";
            } else {
                // line 27
                echo "        <div style=\"display: none;\" class=\"order-modifier-details\">
          <ul>
            ";
                // line 29
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getExcludeSurchargesByType", [0 => $context["sType"]], "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                    // line 30
                    echo "              <li>
                <span class=\"name\">";
                    // line 31
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["row"], "getName", [], "method"), "html", null, true);
                    echo ":</span>
                <span class=\"value\">";
                    // line 32
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Surcharge", "surcharge" => $this->getAttribute($context["row"], "getValue", [], "method"), "currency" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getCurrency", [], "method")]]), "html", null, true);
                    echo "</span>
              </li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 35
                echo "          </ul>
        </div>
      ";
            }
            // line 38
            echo "    </li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['sType'], $context['surcharge'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 40
        echo "
</ul>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/checkout/steps/review/parts/items.modifiers.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 40,  117 => 38,  112 => 35,  103 => 32,  99 => 31,  96 => 30,  92 => 29,  88 => 27,  82 => 25,  79 => 24,  73 => 22,  66 => 19,  60 => 16,  57 => 15,  51 => 13,  45 => 11,  43 => 10,  38 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/checkout/steps/review/parts/items.modifiers.twig", "");
    }
}
