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

/* modules/XC/Onboarding/wizard_steps/done/body.twig */
class __TwigTemplate_e7600ee7605bb45ae46f8e7fcd95410cd2adcaae63c3cee38749b56f093f0d5b extends \XLite\Core\Templating\Twig\Template
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
<div class=\"onboarding-wizard-step step-";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStepIndex", [], "method"), "html", null, true);
        echo "\"
     v-show=\"isCurrentStep('";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStepIndex", [], "method"), "html", null, true);
        echo "')\"
     :transition=\"stepTransition\">
  <div class=\"step-contents\">
    <h2 class=\"heading\">";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Excellent! Storefront is ready to get orders!"]), "html", null, true);
        echo "</h2>
    <p class=\"text\">
      ";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Go on, make your first sale."]), "html", null, true);
        echo "<br>
    </p>

    <div class=\"action-buttons\">
      <div class=\"next-step\">
        ";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Link", "label" => "View dashboard", "attributes" => ["@click" => "closeWizard"], "style" => "regular-button", "jsCode" => "null;"]]), "html", null, true);
        echo "
        ";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Link", 1 => ["label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Open storefront"]), "href" => $this->getAttribute(($context["this"] ?? null), "getStorefrontUrl", [], "method"), "style" => "regular-button", "blank" => true]]]), "html", null, true);
        echo "
      </div>
    </div>

    <div class=\"advertise\">
      <img src=\"";
        // line 22
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["modules/XC/Onboarding/images/addons-ad.png"]), "html", null, true);
        echo "\">
      <p class=\"text\">";
        // line 23
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["What about adding some marketing magic to your sales process?"]);
        echo "
        ";
        // line 24
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\SimpleLink", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Browse Top Trending Tools"]), "attributes" => ["@click" => "browseTools"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      </p>
    </div>

    <div class=\"contacts\">
      <div class=\"phone-email text\">
        <div class=\"phone\">
          ";
        // line 31
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Sales & Customer Service: [phone]", ["phone" => $this->getAttribute(($context["this"] ?? null), "getPhoneNumber", [], "method")]]), "html", null, true);
        echo "
        </div>
        <div class=\"separator\">/</div>
        <div class=\"email\">
          <a href=\"mailto:";
        // line 35
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSalesEmail", [], "method"), "html", null, true);
        echo "\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSalesEmail", [], "method"), "html", null, true);
        echo "</a>
        </div>
      </div>
      <div class=\"knowledge-base-text text\">
        <a href=\"";
        // line 39
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getKBUrl", [], "method"), "html", null, true);
        echo "\" target=\"_blank\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Knowledge base"]), "html", null, true);
        echo "</a>
      </div>
    </div>
  </div>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_steps/done/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  102 => 39,  93 => 35,  86 => 31,  76 => 24,  72 => 23,  68 => 22,  60 => 17,  56 => 16,  48 => 11,  43 => 9,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_steps/done/body.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_steps/done/body.twig");
    }
}
