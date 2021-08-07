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

/* top_links/version_notes/parts/notice.twig */
class __TwigTemplate_d102e94609832a2eb603bd7661810cee7360a910a2fef2f46443c822d5bbc64e extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"activate-notice\">

  <span class=\"trial-notice\">
    ";
        // line 7
        if (($this->getAttribute(($context["this"] ?? null), "getTrialPeriodLeft", [], "method") > 0)) {
            // line 8
            echo "      ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Your X-Cart trial expires in X days", ["count" => $this->getAttribute(($context["this"] ?? null), "getTrialPeriodLeft", [], "method"), "url" => $this->getAttribute(($context["this"] ?? null), "getBusinessViewUrl", [], "method")]]);
            echo "
    ";
        } else {
            // line 10
            echo "      ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Trial has expired!"]), "html", null, true);
            echo "
    ";
        }
        // line 12
        echo "  </span>

  ";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\ActivateKey", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Activate license key"])]]), "html", null, true);
        echo "

  ";
        // line 16
        if ($this->getAttribute(($context["this"] ?? null), "isTrialNoticeAutoDisplay", [], "method")) {
            // line 17
            echo "    ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\TrialNotice"]]), "html", null, true);
            echo "
  ";
        }
        // line 19
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "top_links/version_notes/parts/notice.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 19,  60 => 17,  58 => 16,  53 => 14,  49 => 12,  43 => 10,  37 => 8,  35 => 7,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{##
 # Header notice
 #}
<div class=\"activate-notice\">

  <span class=\"trial-notice\">
    {% if this.getTrialPeriodLeft() > 0 %}
      {{ t('Your X-Cart trial expires in X days', {count: this.getTrialPeriodLeft(), url: this.getBusinessViewUrl()})|raw }}
    {% else %}
      {{ t('Trial has expired!') }}
    {% endif %}
  </span>

  {{ widget('XLite\\\\View\\\\Button\\\\ActivateKey', label=t('Activate license key')) }}

  {% if this.isTrialNoticeAutoDisplay() %}
    {{ widget('XLite\\\\View\\\\Button\\\\TrialNotice') }}
  {% endif %}
</div>
", "top_links/version_notes/parts/notice.twig", "/mff/xcart/skins/admin/top_links/version_notes/parts/notice.twig");
    }
}
