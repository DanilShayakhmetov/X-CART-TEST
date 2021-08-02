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

/* authorization/authorization.twig */
class __TwigTemplate_0c6711b498c28d66caeeed808a367e0c6c2073f12332ba920a44f20a79943c43 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "customer.signin"]]), "html", null, true);
        echo "

";
        // line 7
        $this->startForm($this->getAttribute(($context["this"] ?? null), "getFormClass", [], "method"));        // line 8
        echo "
<table class=\"login-form";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isLocked", [], "method")) {
            echo " locked-out";
        }
        echo "\" ";
        if ($this->getAttribute(($context["this"] ?? null), "isLocked", [], "method")) {
            echo "data-time-left=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTimeLeftToUnlock", [], "method"), "html", null, true);
            echo "\"";
        }
        echo ">

  ";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "customer.signin.fields"]]), "html", null, true);
        echo "

  <tbody class=\"timer-table-body\">
  <tr class=\"timer-header\">
    <td>";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Login is locked out"]), "html", null, true);
        echo "</td>
  </tr>
  <tr class=\"timer-body\">
    <td>
      ";
        // line 19
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Time left"]), "html", null, true);
        echo ": <span id=\"timer\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTimeLeftFormatted", [], "method"), "html", null, true);
        echo "</span>
    </td>
  </tr>
  </tbody>
</table>

";
        $this->endForm();        // line 26
        echo "
";
        // line 27
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "customer.signin.after"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "authorization/authorization.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  83 => 27,  80 => 26,  69 => 19,  62 => 15,  55 => 11,  42 => 9,  39 => 8,  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "authorization/authorization.twig", "/mff/xcart/skins/customer/authorization/authorization.twig");
    }
}
