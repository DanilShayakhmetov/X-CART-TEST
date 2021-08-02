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

/* trial_notice_v2/body.twig */
class __TwigTemplate_2a8eb68654f62d1c9cd712d983c2a493cc9dc334b26b11fcc36a5011aff4d084 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"trial-notice-block";
        // line 5
        if ( !$this->getAttribute(($context["this"] ?? null), "isPopup", [], "method")) {
            echo " alert alert-warning";
        }
        echo "\">
  ";
        // line 6
        if (( !$this->getAttribute(($context["this"] ?? null), "isPopup", [], "method") && $this->getAttribute(($context["this"] ?? null), "isTrialPeriodExpired", [], "method"))) {
            // line 7
            echo "    <h2 class=\"title\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Your X-Cart trial has expired!"]), "html", null, true);
            echo "</h2>
  ";
        }
        // line 9
        echo "
  <div class=\"notice\">
    ";
        // line 11
        if ($this->getAttribute(($context["this"] ?? null), "isTrialPeriodExpired", [], "method")) {
            // line 12
            echo "      ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["You must register your X-Cart installation before using it for real sales. Please enter your license key in the field below or contact our Solution Advisors to get one."]);
            echo "
    ";
        } else {
            // line 14
            echo "      ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["You must register your X-Cart installation before using it for real sales. Activate your existing license key or contact our Solution Advisors to get one."]);
            echo "
    ";
        }
        // line 16
        echo "  </div>

  ";
        // line 18
        if ($this->getAttribute(($context["this"] ?? null), "isTrialPeriodExpired", [], "method")) {
            // line 19
            echo "    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("trial_notice_v2/activate_key.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate("trial_notice_v2/activate_key.twig", "trial_notice_v2/body.twig", 19)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 20
            echo "
    ";
            // line 21
            if ( !$this->getAttribute(($context["this"] ?? null), "isPopup", [], "method")) {
                // line 22
                echo "      <hr/>
      <div class=\"important\">";
                // line 23
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["This message can be removed only through activation of a premium license."]), "html", null, true);
                echo "</div>
      <div class=\"faq\">";
                // line 24
                echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Refer to X-Cart license agreement for further details.", ["licenseAgreementURL" => $this->getAttribute(($context["this"] ?? null), "getLicenseAgreementURL", [], "method")]]);
                echo "</div>
    ";
            }
            // line 26
            echo "  ";
        } else {
            // line 27
            echo "  <div class=\"trial-in-progress\">
    ";
            // line 28
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => "Remind me on next sign-in", "style" => "remind-on-next-sign-in", "jsCode" => "popup.close();", "attributes" => ["data-segment-click" => "Remind me on next sign-in"]]]), "html", null, true);
            echo "
    ";
            // line 29
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => "Contact X-Cart", "style" => "purchase-license regular-main-button", "jsCode" => (("window.open('" . $this->getAttribute(($context["this"] ?? null), "getPurchaseURL", [], "method")) . "', '_blank');"), "attributes" => ["data-segment-click" => "Contact X-Cart"]]]), "html", null, true);
            echo "
  </div>

  <div class=\"register-license-key\">
    <a href=\"#\" class=\"open-license-key-form\">";
            // line 33
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["I have a license key"]), "html", null, true);
            echo "</a>
    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("trial_notice_v2/activate_key.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            // line 34
            $this->loadTemplate("trial_notice_v2/activate_key.twig", "trial_notice_v2/body.twig", 34)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 35
            echo "  </div>
  ";
        }
        // line 37
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "trial_notice_v2/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  134 => 37,  130 => 35,  125 => 34,  116 => 33,  109 => 29,  105 => 28,  102 => 27,  99 => 26,  94 => 24,  90 => 23,  87 => 22,  85 => 21,  82 => 20,  71 => 19,  69 => 18,  65 => 16,  59 => 14,  53 => 12,  51 => 11,  47 => 9,  41 => 7,  39 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "trial_notice_v2/body.twig", "/mff/xcart/skins/admin/trial_notice_v2/body.twig");
    }
}
