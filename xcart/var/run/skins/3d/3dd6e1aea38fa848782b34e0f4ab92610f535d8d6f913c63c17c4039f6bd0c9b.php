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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/settings/PaypalCommercePlatform/payment_status.twig */
class __TwigTemplate_6227c0d9444a6470c993d1ccbd083c357efb6761beedc10ed308ff0dcf0f9a1a extends \XLite\Core\Templating\Twig\Template
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
        $context["signUpUrl"] = $this->getAttribute(($context["this"] ?? null), "getSignUpUrl", [], "method");
        // line 7
        if (($context["signUpUrl"] ?? null)) {
            // line 8
            echo "  ";
            if ($this->getAttribute(($context["this"] ?? null), "isLocalHost", [], "method")) {
                // line 9
                echo "    ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Connecting to PayPal with X-Cart installed on the local server is supported in manual mode only."]), "html", null, true);
                echo "
  ";
            } else {
                // line 11
                echo "    ";
                if ( !($this->getAttribute($this->getAttribute(($context["this"] ?? null), "paymentMethod", []), "getSetting", [0 => "client_id"], "method") && $this->getAttribute($this->getAttribute(($context["this"] ?? null), "paymentMethod", []), "getSetting", [0 => "client_secret"], "method"))) {
                    // line 12
                    echo "      ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Have a PayPal account?"]), "html", null, true);
                    echo "
      <a href=\"";
                    // line 13
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSignUpUrl", [], "method"), "html", null, true);
                    echo "\" target=\"PPFrame\" data-paypal-onboard-complete=\"PaypalCommercePlatformOnboardedCallback\" data-paypal-button=\"true\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Connect with PayPal"]), "html", null, true);
                    echo "</a>
      <br/>
      ";
                    // line 15
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Don't have account yet?"]), "html", null, true);
                    echo "
      <a href=\"";
                    // line 16
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSignUpUrl", [], "method"), "html", null, true);
                    echo "\" target=\"PPFrame\" data-paypal-onboard-complete=\"PaypalCommercePlatformOnboardedCallback\" data-paypal-button=\"true\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Sign Up Now"]), "html", null, true);
                    echo "</a>
    ";
                } else {
                    // line 18
                    echo "      ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Want to use another account?"]), "html", null, true);
                    echo "
      <a href=\"";
                    // line 19
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSignUpUrl", [], "method"), "html", null, true);
                    echo "\" target=\"PPFrame\" data-paypal-onboard-complete=\"PaypalCommercePlatformOnboardedCallback\" data-paypal-button=\"true\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Change"]), "html", null, true);
                    echo "</a>
    ";
                }
                // line 21
                echo "  ";
            }
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/settings/PaypalCommercePlatform/payment_status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  81 => 21,  74 => 19,  69 => 18,  62 => 16,  58 => 15,  51 => 13,  46 => 12,  43 => 11,  37 => 9,  34 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/settings/PaypalCommercePlatform/payment_status.twig", "");
    }
}
