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

/* /mff/xcart/skins/customer/modules/XC/FastLaneCheckout/sections/shipping/address.twig */
class __TwigTemplate_19ccc53185a4bba8120dd7d827172fc8f9b6048979b8eef8a185ebdd5501c302 extends \XLite\Core\Templating\Twig\Template
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
";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\FastLaneCheckout\\View\\Blocks\\Address\\Shipping"]]), "html", null, true);
        echo "

";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isAddressBookVisible", [], "method")) {
            // line 10
            echo "  <div class=\"checkout_fastlane_change_address\">
    ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\AddressBook", "style" => "always-reload", "addressType" => "s", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Choose another address"])]]), "html", null, true);
            echo "
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XC/FastLaneCheckout/sections/shipping/address.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 11,  40 => 10,  38 => 9,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/XC/FastLaneCheckout/sections/shipping/address.twig", "");
    }
}
