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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/FastLaneCheckout/blocks/address_form/billing/same.twig */
class __TwigTemplate_e2ae14b47ddcb8341c54dcd5ed123ca508ed6134b67b5d5136d1bb5f887ea32a extends \XLite\Core\Templating\Twig\Template
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
        $context["visibility"] = (($this->getAttribute(($context["this"] ?? null), "isSameAddressVisible", [], "method")) ? ("") : ("hidden"));
        // line 8
        echo "
<div class=\"checkout_fastlane_block_same_address ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["visibility"] ?? null), "html", null, true);
        echo "\">
  <div class=\"checkbox\">
      <label>
        <input id=\"same_address\" type=\"checkbox\" name=\"same_address\" value=\"1\" v-model=\"same_address\" v-bind:true-value=\"1\" v-bind:false-value=\"0\" ";
        // line 12
        if ($this->getAttribute(($context["this"] ?? null), "isSameAddress", [], "method")) {
            echo " checked=\"checked\" ";
        }
        echo " />
        ";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["The same as shipping"]), "html", null, true);
        echo "
      </label>
  </div>
  <p class=\"help-text\">";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Enable this if you would like us to use your shipping address as your billing address"]), "html", null, true);
        echo "</p>
</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/FastLaneCheckout/blocks/address_form/billing/same.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 16,  50 => 13,  44 => 12,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/FastLaneCheckout/blocks/address_form/billing/same.twig", "");
    }
}
