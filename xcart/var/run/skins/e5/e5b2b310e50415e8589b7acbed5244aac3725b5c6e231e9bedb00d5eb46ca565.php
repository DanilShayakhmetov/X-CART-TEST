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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/payment/methods/parts/action.right.twig */
class __TwigTemplate_690ab0e63905a295e56161e5b5f7cd147b69958b81505f2b64a4bd76e4014f3e extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "hasRightActions", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
            // line 7
            echo "  <div class=\"action right-action\">
  
    ";
            // line 9
            if ($this->getAttribute(($context["this"] ?? null), "canRemoveMethod", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
                // line 10
                echo "      <img src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
                echo "\" class=\"separator\" alt=\"\" />
      <div class=\"remove\"><a href=\"";
                // line 11
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "payment_settings", "remove", ["id" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")]]), "html", null, true);
                echo "\" class=\"fa fa-trash-o\" title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Remove"]), "html", null, true);
                echo "\"></a></div>
    ";
            }
            // line 13
            echo "  
    ";
            // line 14
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getWarningNote", [], "method")) {
                // line 15
                echo "      <img src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
                echo "\" class=\"separator\" alt=\"\" />
      <div class=\"warning\"><a href=\"";
                // line 16
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getConfigurationURL", [], "method"), "html", null, true);
                echo "\" class=\"fa fa-exclamation-circle\" title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getWarningNote", [], "method"), "html", null, true);
                echo "\"></a></div>
    ";
            } elseif ( !$this->getAttribute($this->getAttribute(            // line 17
($context["this"] ?? null), "method", []), "isCurrencyApplicable", [], "method")) {
                // line 18
                echo "      <img src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
                echo "\" class=\"separator\" alt=\"\" />
      <div class=\"warning\"><a href=\"";
                // line 19
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "currency"]), "html", null, true);
                echo "\" class=\"fa fa-exclamation-circle\" title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["This method does not support the current store currency and is not available for customers"]), "html", null, true);
                echo "\"></a></div>
    ";
            } elseif ($this->getAttribute($this->getAttribute(            // line 20
($context["this"] ?? null), "method", []), "isTestMode", [], "method")) {
                // line 21
                echo "      <img src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
                echo "\" class=\"separator\" alt=\"\" />
      <div class=\"test-mode\"><a href=\"";
                // line 22
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getConfigurationURL", [], "method"), "html", null, true);
                echo "\" title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["This method is in test mode"]), "html", null, true);
                echo "\"><img src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
                echo "\" alt=\"\" /></a></div>
    ";
            } elseif ($this->getAttribute($this->getAttribute(            // line 23
($context["this"] ?? null), "method", []), "isConfigurable", [], "method")) {
                // line 24
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Link", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Configure"]), "location" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getConfigurationURL", [], "method"), "style" => "configure"]]), "html", null, true);
                echo "
    ";
            } else {
                // line 26
                echo "    ";
            }
            // line 27
            echo "  
    ";
            // line 28
            if ($this->getAttribute(($context["this"] ?? null), "isSeparateConfigureButtonVisible", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
                // line 29
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Link", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Configure"]), "location" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getConfigurationURL", [], "method"), "style" => "configure"]]), "html", null, true);
                echo "
    ";
            }
            // line 31
            echo "  
  </div>
";
        }
        // line 34
        echo "
";
        // line 35
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => $this->getAttribute(($context["this"] ?? null), "getAfterListName", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method"), "type" => "nested"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/payment/methods/parts/action.right.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 35,  121 => 34,  116 => 31,  110 => 29,  108 => 28,  105 => 27,  102 => 26,  96 => 24,  94 => 23,  86 => 22,  81 => 21,  79 => 20,  73 => 19,  68 => 18,  66 => 17,  60 => 16,  55 => 15,  53 => 14,  50 => 13,  43 => 11,  38 => 10,  36 => 9,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/payment/methods/parts/action.right.twig", "");
    }
}
