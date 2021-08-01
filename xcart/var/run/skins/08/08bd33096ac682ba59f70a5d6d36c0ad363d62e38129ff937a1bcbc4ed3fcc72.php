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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.webmaster.twig */
class __TwigTemplate_286f9dc01760896611943aeddd3f5ee65b2bd96ff5ada2aab91344810e5a7291 extends \XLite\Core\Templating\Twig\Template
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
        $context["webmasterTab"] = twig_constant("XLite\\Module\\XC\\ThemeTweaker\\Core\\ThemeTweaker::MODE_WEBMASTER");
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "isTabAvailable", [0 => ($context["webmasterTab"] ?? null)], "method")) {
            // line 8
            echo "
";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ThemeTweaker\\View\\Button\\ThemeTweakerTab", "svg" => "modules/XC/ThemeTweaker/themetweaker_panel/icons/webmaster.svg", "label" => "Template editor", "style" => ("themetweaker-tab themetweaker-tab_webmaster " . $this->getAttribute(            // line 12
($context["this"] ?? null), "getTabClass", [0 => ($context["webmasterTab"] ?? null)], "method")), "disabled" =>  !$this->getAttribute(            // line 13
($context["this"] ?? null), "isTabAvailable", [0 => ($context["webmasterTab"] ?? null)], "method"), "disabledTooltip" => $this->getAttribute(            // line 14
($context["this"] ?? null), "getTabDisabledTooltip", [0 => ($context["webmasterTab"] ?? null)], "method"), "attributes" => $this->getAttribute(            // line 15
($context["this"] ?? null), "getTabAttributes", [0 => ($context["webmasterTab"] ?? null)], "method")]]), "html", null, true);
            echo "

";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.webmaster.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 15,  40 => 14,  39 => 13,  38 => 12,  37 => 9,  34 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/panel/tabs.webmaster.twig", "");
    }
}
