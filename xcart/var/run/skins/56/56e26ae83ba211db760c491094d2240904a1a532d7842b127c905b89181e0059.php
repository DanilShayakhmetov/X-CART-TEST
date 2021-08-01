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

/* /home/ruslan/Projects/next/output/xcart/src/skins/common/order/invoice/parts/bottom.methods.shipping.twig */
class __TwigTemplate_f7ebf195ecc5e2e3c545487f6a0aac3f9e58b285c4a1c28640eb9e587b46cc45 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"method-box\">
  <strong class=\"method-title\">";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Shipping method"]), "html", null, true);
        echo "</strong>
  ";
        // line 8
        if (($this->getAttribute(($context["this"] ?? null), "getShippingModifier", [], "method") && $this->getAttribute($this->getAttribute(($context["this"] ?? null), "shippingModifier", []), "getMethod", [], "method"))) {
            // line 9
            echo "    ";
            echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "shippingModifier", []), "method", []), "getName", [], "method");
            echo "
  ";
        } elseif ($this->getAttribute($this->getAttribute(        // line 10
($context["this"] ?? null), "order", []), "getShippingMethodName", [], "method")) {
            // line 11
            echo "    ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getShippingMethodName", [], "method")]);
            echo "
  ";
        } else {
            // line 13
            echo "    ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["n/a"]), "html", null, true);
            echo "
  ";
        }
        // line 15
        echo "
  ";
        // line 16
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "trackingNumbers", []), "isEmpty", [], "method")) {
            // line 17
            echo "    <div class=\"tracking-number-box\">
      <strong class=\"method-title\">";
            // line 18
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Tracking numbers"]), "html", null, true);
            echo "</strong>
      <ul class=\"tracking-number-list\">
        ";
            // line 20
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "trackingNumbers", []));
            foreach ($context['_seq'] as $context["_key"] => $context["number"]) {
                // line 21
                echo "          <li>
            ";
                // line 22
                if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationURL", [0 => $this->getAttribute($context["number"], "getValue", [], "method")], "method")) {
                    // line 23
                    echo "              <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationURL", [0 => $this->getAttribute($context["number"], "getValue", [], "method")], "method"), "html", null, true);
                    echo "\" target=\"_blank\">
                ";
                    // line 24
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["number"], "getValue", [], "method"), "html", null, true);
                    echo "
              </a>
            ";
                } else {
                    // line 27
                    echo "              ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["number"], "getValue", [], "method"), "html", null, true);
                    echo "
            ";
                }
                // line 29
                echo "          </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['number'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 31
            echo "      </ul>
    </div>
  ";
        }
        // line 34
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/common/order/invoice/parts/bottom.methods.shipping.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 34,  104 => 31,  97 => 29,  91 => 27,  85 => 24,  80 => 23,  78 => 22,  75 => 21,  71 => 20,  66 => 18,  63 => 17,  61 => 16,  58 => 15,  52 => 13,  46 => 11,  44 => 10,  39 => 9,  37 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/common/order/invoice/parts/bottom.methods.shipping.twig", "");
    }
}
