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

/* modules/CDev/Paypal/onboarding/paypal_commerce_platform/body.twig */
class __TwigTemplate_626e4a867d0ee2bbd47eb2e66ebfa5cfd41304d97d4bbc7b6fcac59a6199c0fb extends \XLite\Core\Templating\Twig\Template
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
<xlite-onboarding-paypal-card inline-template>
  <div class=\"online online-paypal\" :class=\"classes\">

    <div class=\"image\">
      <img src=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["modules/CDev/Paypal/onboarding/paypal_commerce_platform/logo.png"]), "html", null, true);
        echo "\" alt=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["PayPal Checkout"]), "html", null, true);
        echo "\" title=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["PayPal Checkout"]), "html", null, true);
        echo "\" />
    </div>

    <div class=\"note\">
      ";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Setup quickly and offer the convenience and trusted security of PayPal."]), "html", null, true);
        echo "
    </div>

    ";
        // line 16
        if ($this->getAttribute(($context["this"] ?? null), "isPaypalConfigured", [], "method")) {
            // line 17
            echo "      ";
            $context["warningNote"] = $this->getAttribute(($context["this"] ?? null), "getWarningNote", [], "method");
            // line 18
            echo "    <div id=\"paypal_switcher\" class=\"switcher";
            if ($this->getAttribute(($context["this"] ?? null), "isPaypalMethodEnabled", [], "method")) {
                echo " enabled";
            }
            echo " ";
            if (($context["warningNote"] ?? null)) {
                echo "read-only";
            }
            echo "\"";
            if (($context["warningNote"] ?? null)) {
                echo " data-toggle=\"tooltip\" data-html=\"true\" data-title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["warningNote"] ?? null), "html", null, true);
                echo "\" data-container=\"#paypal_switcher\"";
            }
            echo ">
      <span class=\"inactive\">";
            // line 19
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["checkbox.onoff.off"]), "html", null, true);
            echo "</span>
      <a href=\"#\" @click.prevent=\"switchPaypalMethod(";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getMethodId", [], "method"), "html", null, true);
            echo ", \$event)\">
        <div>
          <span class=\"fa fa-check\"></span>
        </div>
      </a>
      <span class=\"active\">";
            // line 25
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["checkbox.onoff.on"]), "html", null, true);
            echo "</span>
    </div>
    ";
        } else {
            // line 28
            echo "    <div class=\"button\">
      ";
            // line 29
            if ($this->getAttribute(($context["this"] ?? null), "isLocalHost", [], "method")) {
                // line 30
                echo "        ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Link", "location" => $this->getAttribute(                // line 31
($context["this"] ?? null), "buildUrl", [0 => "paypal_commerce_platform_settings"], "method"), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Launch (Paypal Onboarding)"]), "blank" => "true"]]), "html", null, true);
                // line 34
                echo "
      ";
            } else {
                // line 36
                echo "        ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\CDev\\Paypal\\View\\Button\\PaypalCommercePlatformOnboarding", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Launch (Paypal Onboarding)"])]]), "html", null, true);
                echo "
      ";
            }
            // line 38
            echo "    </div>
    ";
        }
        // line 40
        echo "  </div>
</xlite-onboarding-paypal-card>";
    }

    public function getTemplateName()
    {
        return "modules/CDev/Paypal/onboarding/paypal_commerce_platform/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  117 => 40,  113 => 38,  107 => 36,  103 => 34,  101 => 31,  99 => 30,  97 => 29,  94 => 28,  88 => 25,  80 => 20,  76 => 19,  59 => 18,  56 => 17,  54 => 16,  48 => 13,  37 => 9,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/CDev/Paypal/onboarding/paypal_commerce_platform/body.twig", "/mff/xcart/skins/admin/modules/CDev/Paypal/onboarding/paypal_commerce_platform/body.twig");
    }
}
