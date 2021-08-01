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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XPay/XPaymentsCloud/order/fraud_status/check.twig */
class __TwigTemplate_b17c7bf36edbe50d351d06c39a88f302a4df8bcdedb5e3800a746fc80675e0cd extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getXpaymentsFraudCheckData", [], "method")) {
            // line 7
            echo "
  ";
            // line 8
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getXpaymentsFraudCheckData", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["data"]) {
                // line 9
                echo "
    <div class=\"antifraud-result\">

      <a name=\"fraud-info-";
                // line 12
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "code", []), "html", null, true);
                echo "\"></a>

      <h2>";
                // line 14
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "service", []), "html", null, true);
                echo " ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["result"]), "html", null, true);
                echo "</h2>

      ";
                // line 16
                if ($this->getAttribute($context["data"], "errors", [])) {
                    // line 17
                    echo "        <div class=\"alert alert-danger\">
          <strong>";
                    // line 18
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Check was not performed due to error occurred!"]), "html", null, true);
                    echo "</strong>
          <ul>
            ";
                    // line 20
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["data"], "getErrorsList", [], "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                        // line 21
                        echo "              <li>";
                        echo $context["error"];
                        echo "</li>
            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 23
                    echo "          </ul>
        </div>
      ";
                }
                // line 26
                echo "
      ";
                // line 27
                if ($this->getAttribute($context["data"], "warnings", [])) {
                    // line 28
                    echo "        <div class=\"alert alert-warning\">
          <strong>";
                    // line 29
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Warning!"]), "html", null, true);
                    echo "</strong>
          <ul>
            ";
                    // line 31
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["data"], "getWarningList", [], "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["warning"]) {
                        // line 32
                        echo "              <li>";
                        echo $context["warning"];
                        echo "</li>
            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['warning'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 34
                    echo "          </ul>
        </div>
      ";
                }
                // line 37
                echo "
      ";
                // line 38
                if ($this->getAttribute($context["data"], "getDisplayMessage", [], "method")) {
                    // line 39
                    echo "        <p class=\"";
                    if ($this->getAttribute($context["data"], "isError", [], "method")) {
                        echo "alert alert-danger";
                    } else {
                        echo "lead";
                    }
                    echo "\">
          ";
                    // line 40
                    if ($this->getAttribute($context["data"], "isError", [], "method")) {
                        // line 41
                        echo "            <strong>";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Check was not performed due to error occurred!"]), "html", null, true);
                        echo "</strong><br />
          ";
                    }
                    // line 43
                    echo "          ";
                    echo $this->getAttribute($context["data"], "getDisplayMessage", [], "method");
                    echo "

          ";
                    // line 45
                    if ($this->getAttribute($context["data"], "score", [])) {
                        // line 46
                        echo "            ";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Score"]), "html", null, true);
                        echo ":
            <span class=\"lead ";
                        // line 47
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "scoreClass", []), "html", null, true);
                        echo "\">";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "score", []), "html", null, true);
                        echo "</span>
          ";
                    }
                    // line 49
                    echo "        </p>
      ";
                }
                // line 51
                echo "
      ";
                // line 52
                if ($this->getAttribute($context["data"], "serviceTransactionId", [])) {
                    // line 53
                    echo "        <p>

          ";
                    // line 55
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Transaction ID"]), "html", null, true);
                    echo ":

          ";
                    // line 57
                    if ($this->getAttribute($context["data"], "url", [])) {
                        // line 58
                        echo "            <a href=\"";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "url", []), "html", null, true);
                        echo "\" target=\"_blank\">";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "serviceTransactionId", []), "html", null, true);
                        echo "</a>
          ";
                    } else {
                        // line 60
                        echo "            ";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["data"], "serviceTransactionId", []), "html", null, true);
                        echo "
          ";
                    }
                    // line 62
                    echo "
        </p>
      ";
                }
                // line 65
                echo "
      ";
                // line 66
                if ($this->getAttribute($context["data"], "rules", [])) {
                    // line 67
                    echo "
        <h3>";
                    // line 68
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Rules triggered"]), "html", null, true);
                    echo ":</h3>

        <ul class=\"antifraud-result-lines\">
          ";
                    // line 71
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["data"], "getRulesList", [], "method"));
                    foreach ($context['_seq'] as $context["_key"] => $context["rule"]) {
                        // line 72
                        echo "            <li>";
                        echo $context["rule"];
                        echo "</li>
          ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['rule'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 74
                    echo "        </ul>
      ";
                }
                // line 76
                echo "
    </div>

  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 80
            echo "
  <br/>

";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XPay/XPaymentsCloud/order/fraud_status/check.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  236 => 80,  227 => 76,  223 => 74,  214 => 72,  210 => 71,  204 => 68,  201 => 67,  199 => 66,  196 => 65,  191 => 62,  185 => 60,  177 => 58,  175 => 57,  170 => 55,  166 => 53,  164 => 52,  161 => 51,  157 => 49,  150 => 47,  145 => 46,  143 => 45,  137 => 43,  131 => 41,  129 => 40,  120 => 39,  118 => 38,  115 => 37,  110 => 34,  101 => 32,  97 => 31,  92 => 29,  89 => 28,  87 => 27,  84 => 26,  79 => 23,  70 => 21,  66 => 20,  61 => 18,  58 => 17,  56 => 16,  49 => 14,  44 => 12,  39 => 9,  35 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XPay/XPaymentsCloud/order/fraud_status/check.twig", "");
    }
}
