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

/* modules/XC/ThemeTweaker/themetweaker/webmaster_mode/webmaster_mode.twig */
class __TwigTemplate_db364a5687148e0c95802730ef2f7bb44aa7324e986598c2698e16c70767da24 extends \XLite\Core\Templating\Twig\Template
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
<xlite-webmaster-mode inline-template interface=\"";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getInterface", [], "method"), "html", null, true);
        echo "\" tree-key=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getJstreeCacheKey", [], "method"), "html", null, true);
        echo "\">
  <div class=\"webmaster-mode-section themetweaker-section\">
    <div class=\"webmaster-mode-tree\" :class=\"treeClasses\">
      <div id=\"themeTweaker_wrapper\" style=\"display: none;\" data-editor-wrapper>
        <div class=\"themeTweaker-control-panel\" data-editor-control-panel>
        </div>
      </div>
    </div>
    <div class=\"webmaster-mode-code\" data-editor-code>
      ";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\ThemeTweaker\\TemplateCode"]]), "html", null, true);
        echo "
    </div>
    ";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("modules/XC/ThemeTweaker/themetweaker/webmaster_mode/confirm_switch_modal.twig");        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        // line 16
        $this->loadTemplate("modules/XC/ThemeTweaker/themetweaker/webmaster_mode/confirm_switch_modal.twig", "modules/XC/ThemeTweaker/themetweaker/webmaster_mode/webmaster_mode.twig", 16)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 17
        echo "  </div>
</xlite-webmaster-mode>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker/webmaster_mode/webmaster_mode.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 17,  57 => 16,  47 => 14,  33 => 5,  30 => 4,);
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
 # Layout editor panel
 #}

<xlite-webmaster-mode inline-template interface=\"{{ this.getInterface() }}\" tree-key=\"{{ this.getJstreeCacheKey() }}\">
  <div class=\"webmaster-mode-section themetweaker-section\">
    <div class=\"webmaster-mode-tree\" :class=\"treeClasses\">
      <div id=\"themeTweaker_wrapper\" style=\"display: none;\" data-editor-wrapper>
        <div class=\"themeTweaker-control-panel\" data-editor-control-panel>
        </div>
      </div>
    </div>
    <div class=\"webmaster-mode-code\" data-editor-code>
      {{ widget('XLite\\\\Module\\\\XC\\\\ThemeTweaker\\\\View\\\\ThemeTweaker\\\\TemplateCode') }}
    </div>
    {% include 'modules/XC/ThemeTweaker/themetweaker/webmaster_mode/confirm_switch_modal.twig' %}
  </div>
</xlite-webmaster-mode>", "modules/XC/ThemeTweaker/themetweaker/webmaster_mode/webmaster_mode.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/webmaster_mode/webmaster_mode.twig");
    }
}
