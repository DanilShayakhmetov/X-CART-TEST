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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/welcome_block/paypal/block.content.twig */
class __TwigTemplate_e802d87afb76d3c2900e10aa3491cd55df5656c1e0665ec3148b1479d97894fe extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"content\">
  <div class=\"info\">

    ";
        // line 9
        if ( !$this->getAttribute(($context["this"] ?? null), "isConfigured", [], "method")) {
            // line 10
            echo "      ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["paypal_commerce_platform_welcome_text"]);
            echo "
      <div class=\"action\">
        ";
            // line 12
            if ($this->getAttribute(($context["this"] ?? null), "isLocalHost", [], "method")) {
                // line 13
                echo "          ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Link", "location" => $this->getAttribute(($context["this"] ?? null), "buildUrl", [0 => "paypal_commerce_platform_settings"], "method"), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Launch PayPal"])]]), "html", null, true);
                echo "
        ";
            } else {
                // line 15
                echo "          ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\CDev\\Paypal\\View\\Button\\PaypalCommercePlatformSignUp", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Launch PayPal"])]]), "html", null, true);
                echo "
        ";
            }
            // line 17
            echo "      </div>
    ";
        } else {
            // line 19
            echo "      ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["paypal_commerce_platform_welcome_text_configured"]);
            echo "
      <div class=\"action\">
        ";
            // line 21
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Link", "location" => $this->getAttribute(($context["this"] ?? null), "buildUrl", [0 => "paypal_commerce_platform_settings"], "method"), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["PayPal Settings"])]]), "html", null, true);
            echo "
      </div>
    ";
        }
        // line 24
        echo "
  </div>

  <div class=\"close-button\"><i class=\"fa fa-times\"></i></div>
  <input type=\"checkbox\" name=\"hide_welcome_block_paypal\" id=\"hide_welcome_block_paypal\" checked=\"checked\"
         class=\"hide-welcome-block\" style=\"display: none\"/>
</div>";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/welcome_block/paypal/block.content.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 24,  67 => 21,  61 => 19,  57 => 17,  51 => 15,  45 => 13,  43 => 12,  37 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/welcome_block/paypal/block.content.twig", "");
    }
}
