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

/* modules/XC/Onboarding/wizard_steps/location/body.twig */
class __TwigTemplate_18b7d154fe6e72805bee913cf466ff6f8a46fa7f54f6e2afff8041b00650bd3b extends \XLite\Core\Templating\Twig\Template
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
  <xlite-wizard-step-location inline-template>
    <div class=\"step-contents\">
      <h2 class=\"heading\">";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Configure your geographic settings to connect with local customers"]), "html", null, true);
        echo "</h2>
      <p class=\"text\">";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["We've tried to guess your country, currency and weight. Verify, please."]), "html", null, true);
        echo "</p>

      <div id=\"location_map\">
        <div id=\"location_map_zones\"></div>
        <div id=\"location_map_markers\"></div>
        <div class=\"point\">
          ";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displaySVGImage", [0 => "modules/XC/Onboarding/images/map-pointer.svg"], "method"), "html", null, true);
        echo "
        </div>
        <div class=\"pulse\"></div>
      </div>

      <div class=\"fields\">
        <div class=\"country\">
          ";
        // line 24
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\Onboarding\\View\\FormField\\Select\\Country", "attributes" => ["v-model" => "country"], "label" => "Country", "value" => $this->getAttribute(($context["this"] ?? null), "getCountry", [], "method"), "fieldId" => "location-country", "stateSelectorId" => "address-state-select", "stateInputId" => "address-custom-state"]]), "html", null, true);
        echo "
          <div class=\"example\"></div>
        </div>
        <div class=\"currency\">
          ";
        // line 28
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\Onboarding\\View\\FormField\\Select\\Currency", "attributes" => ["v-model" => "currency"], "label" => "Currency", "value" => $this->getAttribute(($context["this"] ?? null), "getCurrency", [], "method"), "fieldId" => "location-currency"]]), "html", null, true);
        echo "
          <div class=\"example\">
            ";
        // line 30
        ob_start(function () { return ''; });
        // line 31
        echo "              ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Ex."]), "html", null, true);
        echo "
              <span class=\"prefix\"></span>
              <span class=\"value\">29.99</span>
              <span class=\"suffix\"></span>
            ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        // line 36
        echo "          </div>
        </div>
        <div class=\"weight-unit\">
          ";
        // line 39
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\Onboarding\\View\\FormField\\Select\\WeightUnit", "attributes" => ["v-model" => "weight_unit"], "label" => "Weight", "value" => $this->getAttribute(($context["this"] ?? null), "getWeightUnit", [], "method"), "fieldId" => "location-weight_unit"]]), "html", null, true);
        echo "
          <div class=\"example\">
            ";
        // line 41
        ob_start(function () { return ''; });
        // line 42
        echo "              ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Ex."]), "html", null, true);
        echo "
              <span class=\"value\">2.8</span>
              <span class=\"unit\"></span>
            ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        // line 46
        echo "          </div>
        </div>
      </div>

      <div class=\"buttons\">
        <div class=\"more-button\">
          ";
        // line 52
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Link", "label" => "More localisation settings", "location" => $this->getAttribute(($context["this"] ?? null), "getMoreSettingsLocation", [], "method"), "blank" => 1]]), "html", null, true);
        echo "
        </div>
        <div class=\"next-step\">
          ";
        // line 55
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => "Save and go to the next step", "style" => "regular-main-button", "attributes" => ["@click" => "updateAddress"], "jsCode" => "null;"]]), "html", null, true);
        echo "
        </div>
      </div>
    </div>
  </xlite-wizard-step-location>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_steps/location/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 55,  118 => 52,  110 => 46,  102 => 42,  100 => 41,  95 => 39,  90 => 36,  81 => 31,  79 => 30,  74 => 28,  67 => 24,  57 => 17,  48 => 11,  44 => 10,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_steps/location/body.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_steps/location/body.twig");
    }
}
