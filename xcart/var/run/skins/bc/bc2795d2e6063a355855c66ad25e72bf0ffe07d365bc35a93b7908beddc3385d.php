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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/order/parts/shipping.name.twig */
class __TwigTemplate_10d3ecf6bea19ce5f3edcdc716131a7a68e9fc367ca7ebb3cb9d846b099e21e8 extends \XLite\Core\Templating\Twig\Template
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
<ul class=\"shipping-method-name shipping-method-spec-element\">
  <li class=\"shipping-method-wrapper\">
    <span class=\"shipping-method-label\">";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Shipping method"]), "html", null, true);
        echo ":</span><span
            class=\"shipping-method-name\">";
        // line 10
        echo $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getShippingMethodName", [], "method");
        echo "</span>
  </li>
  ";
        // line 12
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "trackingNumbers", []), "isEmpty", [], "method")) {
            // line 13
            echo "    <li class=\"tracking-number-wrapper\">
      <span class=\"tracking-number-label\">";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Tracking numbers"]), "html", null, true);
            echo ":</span>

      <div class=\"tracking-number-box\">
        ";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "trackingNumbers", []));
            foreach ($context['_seq'] as $context["_key"] => $context["number"]) {
                // line 18
                echo "
          <span class=\"tracking-item\">
          ";
                // line 20
                if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationURL", [0 => $this->getAttribute($context["number"], "value", [])], "method")) {
                    // line 21
                    echo "            ";
                    if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "isTrackingInformationForm", [0 => $this->getAttribute($context["number"], "value", [])], "method")) {
                        // line 22
                        echo "              <form method=\"";
                        echo $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationMethod", [0 => $this->getAttribute($context["number"], "value", [])], "method");
                        echo "\"
                    action=\"";
                        // line 23
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationURL", [0 => $this->getAttribute($context["number"], "value", [])], "method"), "html", null, true);
                        echo "\" target=\"_blank\">
            ";
                        // line 24
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationParams", [0 => $this->getAttribute($context["number"], "value", [])], "method"));
                        foreach ($context['_seq'] as $context["name"] => $context["value"]) {
                            // line 25
                            echo "              <input type=\"hidden\" name=\"";
                            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["name"], "html", null, true);
                            echo "\" value=\"";
                            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["value"], "html", null, true);
                            echo "\"/>
            ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['name'], $context['value'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 27
                        echo "                <span>";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["number"], "value", []), "html", null, true);
                        echo " - </span>
            <button type=\"submit\">";
                        // line 28
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Track package"]), "html", null, true);
                        echo "</button>
          </form>
          ";
                    } else {
                        // line 31
                        echo "              <span>";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["number"], "value", []), "html", null, true);
                        echo " - </span>
              <a href=\"";
                        // line 32
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getTrackingInformationURL", [0 => $this->getAttribute($context["number"], "value", [])], "method"), "html", null, true);
                        echo "\"
                 target=\"_blank\">";
                        // line 33
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Track package"]), "html", null, true);
                        echo "</a>
            ";
                    }
                    // line 35
                    echo "          ";
                } else {
                    // line 36
                    echo "            ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["number"], "value", []), "html", null, true);
                    echo "
          ";
                }
                // line 38
                echo "          </span><br/>

        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['number'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 41
            echo "      </div>
    </li>
  ";
        }
        // line 44
        echo "
</ul>";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/order/parts/shipping.name.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  139 => 44,  134 => 41,  126 => 38,  120 => 36,  117 => 35,  112 => 33,  108 => 32,  103 => 31,  97 => 28,  92 => 27,  81 => 25,  77 => 24,  73 => 23,  68 => 22,  65 => 21,  63 => 20,  59 => 18,  55 => 17,  49 => 14,  46 => 13,  44 => 12,  39 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/order/parts/shipping.name.twig", "");
    }
}
