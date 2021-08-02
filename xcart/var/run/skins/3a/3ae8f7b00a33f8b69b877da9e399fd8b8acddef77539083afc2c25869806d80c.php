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

/* modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/confirm_save_modal.twig */
class __TwigTemplate_7aea3acc21b0ddb1e501670a476b2921a026d310f14317e865549fd054229f58 extends \XLite\Core\Templating\Twig\Template
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
<xlite-themetweaker-modal :show=\"isSaveConfirmVisible\" namespace=\"saveConfirm\">
  <p slot=\"body\" class=\"text-center\">";
        // line 8
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Some edited templates were selected to be disabled. You can enable them on the Edited templates page in the Admin area."]);
        echo "</p>
</xlite-themetweaker-modal>";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/confirm_save_modal.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/confirm_save_modal.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel_extensions/confirm_save_modal.twig");
    }
}
