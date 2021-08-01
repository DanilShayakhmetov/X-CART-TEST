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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/layout_settings/settings/parts/settings.current_template.twig */
class __TwigTemplate_527e6383a53705d7de41c7a9c373cdfe3eebd97b1b6160f588e66ed898d4b397 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"current-template\">
  <span>";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Current template"]), "html", null, true);
        echo ":</span> <span class=\"template-name\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCurrentSkinName", [], "method"), "html", null, true);
        echo "</span>
  ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "showSettingsForm", [], "method")) {
            // line 9
            echo "    <div class=\"settings-form\">
      ";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\SimpleLink", "location" => $this->getAttribute(($context["this"] ?? null), "getSettingsForm", [], "method"), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Edit custom options"])]]), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 13
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/layout_settings/settings/parts/settings.current_template.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 13,  44 => 10,  41 => 9,  39 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/layout_settings/settings/parts/settings.current_template.twig", "");
    }
}
