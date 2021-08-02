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

/* modules/XC/Onboarding/wizard_steps/company_logo_added/body.twig */
class __TwigTemplate_32445296dec8e64461e383d34443451923d7df12eff879da5eb4175639f094e6 extends \XLite\Core\Templating\Twig\Template
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
<div
        class=\"onboarding-wizard-step step-";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStepIndex", [], "method"), "html", null, true);
        echo "\"
        v-show=\"isCurrentStep('";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStepIndex", [], "method"), "html", null, true);
        echo "')\"
        :transition=\"stepTransition\">
  <xlite-wizard-step-company-logo-added inline-template>
    <div class=\"step-contents\">
      <h2 class=\"heading\">";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Go to the next step"]), "html", null, true);
        echo "</h2>
      <p id=\"newLogoUrlCheck\" data-logo=\"";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "lastLogoInclude", [], "method"), "html", null, true);
        echo "\" class=\"text\">";
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Open the storefront and check how it looks like on desktop.", ["shopUrl" => $this->getAttribute(($context["this"] ?? null), "getURLForNewLogoChecking", [], "method")]]);
        echo "</p>
      ";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\SimpleLink", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Upload a new logo"]), "attributes" => ["@click" => "reuploadLogo"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      <div class=\"advertise\">
        <img src=\"";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["modules/XC/Onboarding/images/logo-advertise.png"]), "html", null, true);
        echo "\">
        <p class=\"text\">";
        // line 16
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["100% mobile-friendly eCommerce website templates, fully customizable, affordable, and open source."]);
        echo "
          ";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\SimpleLink", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Visit the template store"]), "attributes" => ["@click" => "visitTemplateStore"], "jsCode" => "null;"]]), "html", null, true);
        echo "
        </p>
      </div>
      <div class=\"buttons\">
        ";
        // line 21
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Save and go to the next step"]), "style" => "regular-main-button", "attributes" => ["@click" => "save"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      </div>
    </div>
  </xlite-wizard-step-company-logo-added>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_steps/company_logo_added/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 21,  68 => 17,  64 => 16,  60 => 15,  55 => 13,  49 => 12,  45 => 11,  38 => 7,  34 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_steps/company_logo_added/body.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_steps/company_logo_added/body.twig");
    }
}
