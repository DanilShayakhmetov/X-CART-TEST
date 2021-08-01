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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/parts/total.modifiers.twig */
class __TwigTemplate_b60e2d12bdd5a4fcd762eb5397e86b76c7513d098f3798ec1b24fdd557c3a450 extends \XLite\Core\Templating\Twig\Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getSurchargeTotals", [], "method"));
        foreach ($context['_seq'] as $context["sType"] => $context["surcharge"]) {
            // line 8
            echo "  <li class=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSurchargeClassName", [0 => $context["sType"], 1 => $context["surcharge"]], "method"), "html", null, true);
            echo "\">
    ";
            // line 9
            if (($this->getAttribute($context["surcharge"], "count", []) == "1")) {
                // line 10
                echo "      <strong>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["surcharge"], "lastName", []), "html", null, true);
                echo ":</strong>
    ";
            } else {
                // line 12
                echo "      <strong class=\"list-owner\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["surcharge"], "name", []), "html", null, true);
                echo ":</strong>
    ";
            }
            // line 14
            echo "    ";
            if ($this->getAttribute($context["surcharge"], "available", [])) {
                // line 15
                echo "      <span class=\"value\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Surcharge", "surcharge" => $this->getAttribute(($context["this"] ?? null), "formatSurcharge", [0 => $context["surcharge"]], "method"), "currency" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getCurrency", [], "method")]]), "html", null, true);
                echo "</span>
    ";
            } else {
                // line 17
                echo "      <span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["n/a"]), "html", null, true);
                echo "</span>
    ";
            }
            // line 19
            echo "    ";
            if (($this->getAttribute($context["surcharge"], "count", []) == "1")) {
                // line 20
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "modifier", "type" => "nested", "surcharge" => $context["surcharge"], "sType" => $context["sType"], "cart" => $this->getAttribute(($context["this"] ?? null), "cart", [])]]), "html", null, true);
                echo "
    ";
            } else {
                // line 22
                echo "      <div style=\"display: none;\" class=\"order-modifier-details\">
        <ul>
          ";
                // line 24
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getExcludeSurchargesByType", [0 => $context["sType"]], "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                    // line 25
                    echo "            <li>
              <span class=\"name\">";
                    // line 26
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["row"], "getName", [], "method"), "html", null, true);
                    echo ":</span>
              <span class=\"value\">";
                    // line 27
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Surcharge", "surcharge" => $this->getAttribute($context["row"], "getValue", [], "method"), "currency" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getCurrency", [], "method")]]), "html", null, true);
                    echo "</span>
            </li>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 30
                echo "        </ul>
      </div>
    ";
            }
            // line 33
            echo "  </li>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['sType'], $context['surcharge'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/parts/total.modifiers.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 33,  104 => 30,  95 => 27,  91 => 26,  88 => 25,  84 => 24,  80 => 22,  74 => 20,  71 => 19,  65 => 17,  59 => 15,  56 => 14,  50 => 12,  44 => 10,  42 => 9,  37 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/parts/total.modifiers.twig", "");
    }
}
