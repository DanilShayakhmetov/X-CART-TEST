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

/* login/body.twig */
class __TwigTemplate_3f83d495751783ff787ec6774e5375c15fc833ecea2a2b006f1b2c2965981f7e extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"login-box-wrapper\">
  <div class=\"login-box";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "isLocked", [], "method")) {
            echo " locked\" data-time-left=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTimeLeftToUnlock", [], "method"), "html", null, true);
        }
        echo "\">

    <h1 class=\"text-capitalize\">";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Administration Zone"]), "html", null, true);
        echo "</h1>

    ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isLocked", [], "method")) {
            // line 10
            echo "      <h2 class=\"timer-header\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Login is locked out"]), "html", null, true);
            echo "</h2>
    ";
        }
        // line 12
        echo "
    ";
        // line 13
        if ($this->getAttribute(($context["this"] ?? null), "additional_note", [])) {
            // line 14
            echo "      <div class=\"additional-note\">";
            echo $this->getAttribute(($context["this"] ?? null), "additional_note", []);
            echo "</div>
    ";
        }
        // line 16
        echo "
    <form id=\"login_form\" action=\"";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "login"]), "html", null, true);
        echo "\" method=\"post\" name=\"login_form\">
      <input type=\"hidden\" name=\"target\" value=\"login\" />
      <input type=\"hidden\" name=\"action\" value=\"login\" />
      ";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\FormId"]]), "html", null, true);
        echo "

      <table>
        <tbody class=\"fields\">
          <tr>
            <td><input type=\"text\" class=\"form-control\" name=\"login\" value=\"";
        // line 25
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "login", []), "html", null, true);
        echo "\" size=\"32\" maxlength=\"128\" autocomplete=\"off\" placeholder=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Email"]), "html", null, true);
        echo "\" /></td>
          </tr>
          <tr>
            <td><input type=\"password\" class=\"form-control\" name=\"password\" value=\"";
        // line 28
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "password", []), "html", null, true);
        echo "\" size=\"32\" maxlength=\"128\" autocomplete=\"off\" placeholder=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Password"]), "html", null, true);
        echo "\" /></td>
          </tr>
        </tbody>

        ";
        // line 32
        if ($this->getAttribute(($context["this"] ?? null), "isLocked", [], "method")) {
            // line 33
            echo "          <tbody class=\"timer\">
            <tr>
              <td>
                ";
            // line 36
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Time left"]), "html", null, true);
            echo ": <span id=\"timer\"></span>
              </td>
            </tr>
          </tbody>
        ";
        }
        // line 41
        echo "
        <tbody class=\"buttons\">
          <tr>
            <td>
              ";
        // line 45
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Submit", "label" => "Log in", "style" => "regular-main-button btn-lg"]]), "html", null, true);
        echo "
              <div class=\"forgot-password\">
                <a href=\"";
        // line 47
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "recover_password"]), "html", null, true);
        echo "\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Forgot password?"]), "html", null, true);
        echo "</a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </form>

  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "login/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 47,  120 => 45,  114 => 41,  106 => 36,  101 => 33,  99 => 32,  90 => 28,  82 => 25,  74 => 20,  68 => 17,  65 => 16,  59 => 14,  57 => 13,  54 => 12,  48 => 10,  46 => 9,  41 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "login/body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/login/body.twig");
    }
}
