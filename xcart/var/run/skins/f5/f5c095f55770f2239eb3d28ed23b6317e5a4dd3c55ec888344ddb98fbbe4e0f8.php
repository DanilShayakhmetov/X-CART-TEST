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

/* modules/XC/Onboarding/wizard_steps/shipping_rates/body.twig */
class __TwigTemplate_af017fce40079e2e55495af269234c9528d65a23690be4fee867d037ad795090 extends \XLite\Core\Templating\Twig\Template
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
  <xlite-wizard-step-shipping-rates inline-template :methods=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, twig_jsonencode_filter($this->getAttribute(($context["this"] ?? null), "getMethodsData", [], "method")), "html", null, true);
        echo "\">
    <div class=\"step-contents\">
      <h2 class=\"heading\">";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Set flat shipping rates for your store"]), "html", null, true);
        echo "</h2>

      <p class=\"text\">
        ";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Here you can add a flat-rate shipping method"]), "html", null, true);
        echo "
      </p>

      ";
        // line 16
        if ($this->getAttribute(($context["this"] ?? null), "getMethods", [], "method")) {
            // line 17
            echo "        <div class=\"shipping-carriers\">
          ";
            // line 18
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getMethods", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["method"]) {
                // line 19
                echo "            <div class=\"shipping-carrier image\" v-if=\"isMethodAvailable('";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["method"], "processor", []), "html", null, true);
                echo "')\" :class=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ("methodClasses." . $this->getAttribute($context["method"], "processor", [])), "html", null, true);
                echo "\" data-processor=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["method"], "processor", []), "html", null, true);
                echo "\">
              <a href=\"";
                // line 20
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSettingsURL", [0 => $context["method"]], "method"), "html", null, true);
                echo "\" target=\"_blank\"
                 @click.prevent=\"addShippingMethod('";
                // line 21
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSettingsURL", [0 => $context["method"]], "method"), "html", null, true);
                echo "', \$event)\">
                <img src=\"";
                // line 22
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["method"], "getAdminIconURL", [], "method"), "html", null, true);
                echo "\" alt=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["method"], "name", []), "html", null, true);
                echo "\"/>
              </a>
            </div>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['method'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 26
            echo "        </div>

        <div class=\"separator\">
          ";
            // line 29
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Here you can manually define your shipping rates and etc."]), "html", null, true);
            echo "
          <a href=\"";
            // line 30
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getArticleLink", [], "method"), "html", null, true);
            echo "\" target=\"_blank\" class=\"external\" style=\"margin-left: 5px;\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["How to define shipping rates"]), "html", null, true);
            echo "</a>
        </div>
      ";
        }
        // line 33
        echo "

      <div class=\"my-shipping\">
        <div class=\"method-label\">
          <p class=\"input-display\" v-text=\"name\" v-show=\"!focusedName\" tabindex=\"-1\">";
        // line 37
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getDefaultMethodName", [], "method"), "html", null, true);
        echo "</p>
          <div class=\"input-wrapper\" v-show=\"focusedName\">
            ";
        // line 39
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\Text", "fieldOnly" => true, "attributes" => ["v-model" => "name"], "value" => $this->getAttribute(($context["this"] ?? null), "getDefaultMethodName", [], "method")]]), "html", null, true);
        echo "
          </div>
        </div>
        <div class=\"zones\">
          ";
        // line 43
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Select\\ShippingZone", "fieldOnly" => true]]), "html", null, true);
        echo "
        </div>
        <div class=\"flat-rate\">
          ";
        // line 46
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\Text\\Price", "fieldOnly" => true, "placeholder" => "0.00"]]), "html", null, true);
        echo "
        </div>
      </div>

      <div class=\"buttons\">
        <div class=\"more-button\">
          ";
        // line 52
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Link", "label" => "More shipping methods", "location" => $this->getAttribute(($context["this"] ?? null), "getMoreSettingsLocation", [], "method"), "blank" => 1]]), "html", null, true);
        echo "
        </div>
        <div class=\"next-step\">
          ";
        // line 55
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Regular", "label" => "Create shipping rate", "style" => "regular-main-button", "attributes" => ["@click" => "createMethod"], "jsCode" => "null;"]]), "html", null, true);
        echo "
        </div>
      </div>

      <div class=\"popup-template\">
        <div class=\"method-added-popup\">
          <div class=\"image\">
            <img src=\"\" alt=\"\"/>
          </div>

          <div class=\"note\">
            ";
        // line 66
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Bingo! You've added carrier as a shipping carrier"]), "html", null, true);
        echo "
          </div>

          <div class=\"settings-link\">
          </div>

          <div class=\"buttons\">
            <a class=\"settings-link btn regular-button\" href=\"\" target=\"_blank\">
              <span>";
        // line 74
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Module settings (shipping_rates)"]), "html", null, true);
        echo "</span>
            </a>
            ";
        // line 76
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Regular", "label" => "Proceed to the next step", "style" => "regular-main-button", "jsCode" => "null;"]]), "html", null, true);
        echo "
          </div>
        </div>
      </div>
    </div>
  </xlite-wizard-step-shipping-rates>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_steps/shipping_rates/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  183 => 76,  178 => 74,  167 => 66,  153 => 55,  147 => 52,  138 => 46,  132 => 43,  125 => 39,  120 => 37,  114 => 33,  106 => 30,  102 => 29,  97 => 26,  85 => 22,  81 => 21,  77 => 20,  68 => 19,  64 => 18,  61 => 17,  59 => 16,  53 => 13,  47 => 10,  42 => 8,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_steps/shipping_rates/body.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_steps/shipping_rates/body.twig");
    }
}
