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

/* modules/XC/Onboarding/wizard_steps/company_info/body.twig */
class __TwigTemplate_a108ae653fc23eaa3a5ccd32d268e8f9708ab86062dee4f4c658e8cb04d594ce extends \XLite\Core\Templating\Twig\Template
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
  <xlite-wizard-step-company-info inline-template>
    <div class=\"step-contents\">
      <h2 class=\"heading\">";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Set your company info"]), "html", null, true);
        echo "</h2>
      <p class=\"text\">";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["We will use this info to create invoices, send emails and calculate shipping rates for your customers."]), "html", null, true);
        echo "</p>

      ";
        // line 13
        $this->startForm("XLite\\Module\\XC\\Onboarding\\View\\Form\\CompanyInfo");        // line 14
        echo "        <div class=\"fields\">
          ";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "label" => "Company name", "fieldName" => "company_name", "value" => $this->getAttribute(($context["this"] ?? null), "getCompanyName", [], "method")]]), "html", null, true);
        echo "
          ";
        // line 16
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "label" => "Address", "fieldName" => "address", "value" => $this->getAttribute(($context["this"] ?? null), "getAddress", [], "method")]]), "html", null, true);
        echo "
          <div class=\"parent-block\">
            ";
        // line 18
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Select\\State", "fieldName" => "address_state_select", "label" => "State", "value" => $this->getAttribute(($context["this"] ?? null), "getState", [], "method"), "country" => $this->getAttribute(($context["this"] ?? null), "getCountry", [], "method")]]), "html", null, true);
        echo "
          </div>
          <div class=\"parent-block\">
            ";
        // line 21
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "fieldName" => "address_custom_state", "value" => $this->getAttribute(($context["this"] ?? null), "getOtherState", [], "method"), "label" => "State"]]), "html", null, true);
        echo "
          </div>
          ";
        // line 23
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "label" => "Phone", "fieldName" => "phone", "value" => $this->getAttribute(($context["this"] ?? null), "getPhone", [], "method")]]), "html", null, true);
        echo "
          ";
        // line 24
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "label" => "City", "fieldName" => "city", "value" => $this->getAttribute(($context["this"] ?? null), "getCity", [], "method")]]), "html", null, true);
        echo "
          ";
        // line 25
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Text", "label" => "Zip code", "fieldName" => "zipcode", "value" => $this->getAttribute(($context["this"] ?? null), "getZipCode", [], "method")]]), "html", null, true);
        echo "
        </div>

        <div class=\"buttons bottom-sticky\">
          <div class=\"next-step\">
            ";
        // line 30
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Submit", "label" => "Save and go to the next step", "style" => "action regular-main-button"]]), "html", null, true);
        echo "
          </div>
        </div>
      ";
        $this->endForm();        // line 34
        echo "    </div>
  </xlite-wizard-step-company-info>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_steps/company_info/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  99 => 34,  93 => 30,  85 => 25,  81 => 24,  77 => 23,  72 => 21,  66 => 18,  61 => 16,  57 => 15,  54 => 14,  53 => 13,  48 => 11,  44 => 10,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_steps/company_info/body.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_steps/company_info/body.twig");
    }
}
