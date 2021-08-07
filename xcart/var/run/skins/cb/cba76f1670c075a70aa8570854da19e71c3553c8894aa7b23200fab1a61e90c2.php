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

/* header/parts/script_config.twig */
class __TwigTemplate_bd995f588b15d91932b95ea271f62fb2b581f4781f6aabfcae59a15433a811ae extends \XLite\Core\Templating\Twig\Template
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
<script>
  var xliteConfig = {
    script: '";
        // line 9
        echo $this->getAttribute(($context["this"] ?? null), "getScript", [], "method");
        echo "',

    ";
        // line 11
        if ($this->getAttribute(($context["this"] ?? null), "isDisplayAdminScript", [], "method")) {
            // line 12
            echo "    admin_script: '";
            echo $this->getAttribute(($context["this"] ?? null), "getAdminScript", [], "method");
            echo "',
    ";
        }
        // line 14
        echo "
    zone: 'customer',
    target: '";
        // line 16
        echo $this->getAttribute(($context["this"] ?? null), "getTarget", [], "method");
        echo "',
    language: '";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "currentLanguage", []), "getCode", [], "method"), "html", null, true);
        echo "',
    ajax_prefix: '";
        // line 18
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAjaxPrefix", [], "method"), "html", null, true);
        echo "',
    form_id: '";
        // line 19
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "xlite", []), "formId", []), "html", null, true);
        echo "',
    form_id_name: '";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, twig_constant("XLite::FORM_ID"), "html", null, true);
        echo "',
    developer_mode: ";
        // line 21
        echo (($this->getAttribute(($context["this"] ?? null), "isDeveloperMode", [], "method")) ? ("true") : ("false"));
        echo ",
    clean_url: ";
        // line 22
        echo (($this->getAttribute(($context["this"] ?? null), "isCleanUrlsEnabled", [], "method")) ? ("true") : ("false"));
        echo ",
    clean_urls_base: '";
        // line 23
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "cleansUrlsBase", [], "method"), "html", null, true);
        echo "',
  };
</script>
";
    }

    public function getTemplateName()
    {
        return "header/parts/script_config.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 23,  76 => 22,  72 => 21,  68 => 20,  64 => 19,  60 => 18,  56 => 17,  52 => 16,  48 => 14,  42 => 12,  40 => 11,  35 => 9,  30 => 6,);
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
 # Head list children
 #
 # @ListChild (list=\"head\", weight=\"1300\")
 #}

<script>
  var xliteConfig = {
    script: '{{ this.getScript()|raw }}',

    {% if this.isDisplayAdminScript() %}
    admin_script: '{{ this.getAdminScript()|raw }}',
    {% endif %}

    zone: 'customer',
    target: '{{ this.getTarget()|raw }}',
    language: '{{ this.currentLanguage.getCode() }}',
    ajax_prefix: '{{ this.getAjaxPrefix() }}',
    form_id: '{{ this.xlite.formId }}',
    form_id_name: '{{ constant('XLite::FORM_ID') }}',
    developer_mode: {{ this.isDeveloperMode() ? 'true' : 'false' }},
    clean_url: {{ this.isCleanUrlsEnabled() ? 'true' : 'false' }},
    clean_urls_base: '{{ this.cleansUrlsBase() }}',
  };
</script>
", "header/parts/script_config.twig", "/mff/xcart/skins/customer/header/parts/script_config.twig");
    }
}
