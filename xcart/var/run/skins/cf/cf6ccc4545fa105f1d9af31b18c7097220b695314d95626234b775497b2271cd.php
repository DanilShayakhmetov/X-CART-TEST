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

/* modules/XC/Onboarding/wizard_progress/progress.twig */
class __TwigTemplate_5cad9ef62356a76f25187292723790646f07532db8ad3deaf6d9e55b22d488f4 extends \XLite\Core\Templating\Twig\Template
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
        // line 1
        echo "<xlite-wizard-progress inline-template :step=\"step\" :steps=\"steps\" :landmarks=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, twig_jsonencode_filter($this->getAttribute(($context["this"] ?? null), "getLandmarks", [], "method")), "html", null, true);
        echo "\" :current-step=\"currentStep\" :last-product=\"lastProduct\">
  <div class=\"";
        // line 2
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getDivStyle", [], "method"), "html", null, true);
        echo "\" v-data='{\"progress\": ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPercentage", [], "method"), "html", null, true);
        echo " }'>
    <div class=\"percentage\" v-text=\"this.progress + '%'\" v-if=\"!isCurrentStep('intro')\" transition=\"fade-in-out\">";
        // line 3
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($this->getAttribute(($context["this"] ?? null), "getPercentage", [], "method") . "%"), "html", null, true);
        echo "</div>
    <div class=\"bar\">
      <div class=\"landmarks\">
        ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getLandmarks", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["step"]) {
            // line 7
            echo "          <div v-if=\"'";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["step"], "index", []), "html", null, true);
            echo "' === '";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFirstIndex", [], "method"), "html", null, true);
            echo "' && isCurrentStep('intro')\"
               class=\"landmark landmark-";
            // line 8
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["step"], "index", []), "html", null, true);
            echo "\"
               :class=\"";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ("landmarkClass." . $this->getAttribute($context["step"], "index", [])), "html", null, true);
            echo "\"
               tabindex=\"-1\">
            ";
            // line 11
            echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, $this->getAttribute($context["step"], "image", [])]);
            echo "
            ";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\SimpleLink", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute($context["step"], "name", [])]), "attributes" => ["@click" => "goToNextStep", "transition" => "fade-in-out"], "jsCode" => "null;", "style" => "landmark-text"]]), "html", null, true);
            echo "
          </div>
          <div v-else
               class=\"landmark landmark-";
            // line 15
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["step"], "index", []), "html", null, true);
            echo "\"
               :class=\"";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ("landmarkClass." . $this->getAttribute($context["step"], "index", [])), "html", null, true);
            echo "\"
               @click=\"goToStep('";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["step"], "index", []), "html", null, true);
            echo "')\"
               tabindex=\"-1\">
            ";
            // line 19
            echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, $this->getAttribute($context["step"], "image", [])]);
            echo "
            <span v-if=\"isCurrentStep('intro')\" transition=\"fade-in-out\" class=\"landmark-text\">";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute($context["step"], "name", [])]), "html", null, true);
            echo "</span>
          </div>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['step'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo "      </div>
      <div class=\"progress-line\">
        <div class=\"progress-line-filled\" style=\"width: ";
        // line 25
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($this->getAttribute(($context["this"] ?? null), "getPercentage", [], "method") . "%"), "html", null, true);
        echo "\" :style=\"barStyle\"></div>
      </div>
    </div>
    <div class=\"finish-mark\" :class=\"finishClass\" v-if=\"!isCurrentStep('intro')\" transition=\"fade-in-out\">
      ";
        // line 29
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "modules/XC/Onboarding/images/ok-mark.svg"]);
        echo "
    </div>
  </div>
</xlite-wizard-progress>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_progress/progress.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  114 => 29,  107 => 25,  103 => 23,  94 => 20,  90 => 19,  85 => 17,  81 => 16,  77 => 15,  71 => 12,  67 => 11,  62 => 9,  58 => 8,  51 => 7,  47 => 6,  41 => 3,  35 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_progress/progress.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_progress/progress.twig");
    }
}
