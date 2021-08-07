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

/* modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/code_error_modal.twig */
class __TwigTemplate_6c1d048b7557197a0f391f8c215ede1e07c24aab4483a34b03f6cd309c1f7357 extends \XLite\Core\Templating\Twig\Template
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
<xlite-themetweaker-modal :show=\"isErrorDialogVisible\" namespace=\"errorDialog\">
  <p slot=\"body\" class=\"text-center\" v-text=\"errorMessage\"></p>
  <div slot=\"footer\">
    <button class=\"themetweaker-modal-button secondary\"
            v-if=\"callbacks.errorDialog.cancel\"
            @click=\"onErrorDialogCancel\">
      <span>";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Cancel"]), "html", null, true);
        echo "</span>
    </button>
    <button class=\"themetweaker-modal-button\"
            @click=\"onErrorDialogOk\">
      <span>";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["OK"]), "html", null, true);
        echo "</span>
    </button>
  </div>
</xlite-themetweaker-modal>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/code_error_modal.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 17,  39 => 13,  30 => 6,);
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
 #
 # @ListChild(list=\"themetweaker-panel-extensions\", weight=\"220\")
 #}

<xlite-themetweaker-modal :show=\"isErrorDialogVisible\" namespace=\"errorDialog\">
  <p slot=\"body\" class=\"text-center\" v-text=\"errorMessage\"></p>
  <div slot=\"footer\">
    <button class=\"themetweaker-modal-button secondary\"
            v-if=\"callbacks.errorDialog.cancel\"
            @click=\"onErrorDialogCancel\">
      <span>{{ t('Cancel') }}</span>
    </button>
    <button class=\"themetweaker-modal-button\"
            @click=\"onErrorDialogOk\">
      <span>{{ t('OK') }}</span>
    </button>
  </div>
</xlite-themetweaker-modal>", "modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/code_error_modal.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/code_error_modal.twig");
    }
}
