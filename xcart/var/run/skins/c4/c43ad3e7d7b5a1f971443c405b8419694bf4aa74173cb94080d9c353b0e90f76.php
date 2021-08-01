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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XPay/XPaymentsCloud/invoice/invoice.twig */
class __TwigTemplate_8b0b842ce30efb646eed02bc66a7c81ebe026bb77ca916f82f5005b330c0d7ea extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getXpaymentsCards", [], "method")) {
            // line 7
            echo "    <div class=\"xpayments-card-box\">

      <strong>";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Used credit or debit cards"]), "html", null, true);
            echo "</strong>
      ";
            // line 10
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getXpaymentsCards", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["card"]) {
                // line 11
                echo "        <div class=\"xpayments-card\">
          <span class=\"card-icon-container\">
            <span class=\"xpayments-card-type ";
                // line 13
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["card"], "cssType", []), "html", null, true);
                echo "\">
              <img src=\"skins/customer/images/spacer.gif\" alt=\"";
                // line 14
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["card"], "cardType", []), "html", null, true);
                echo "\" />
            </span>
          </span>
          ";
                // line 17
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["card"], "cardNumber", []), "html", null, true);
                echo "
          ";
                // line 18
                if ($this->getAttribute($context["card"], "expire", [])) {
                    // line 19
                    echo "            <span>(";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["card"], "expire", []), "html", null, true);
                    echo ")</span>
          ";
                }
                // line 21
                echo "        </div>
        <br />
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['card'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 24
            echo "
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XPay/XPaymentsCloud/invoice/invoice.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 24,  70 => 21,  64 => 19,  62 => 18,  58 => 17,  52 => 14,  48 => 13,  44 => 11,  40 => 10,  36 => 9,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XPay/XPaymentsCloud/invoice/invoice.twig", "");
    }
}
