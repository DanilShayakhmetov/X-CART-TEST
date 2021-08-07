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

/* /mff/xcart/skins/admin/zones/details/parts/field.state.twig */
class __TwigTemplate_76c5c4b6ec5fe9ddbf7d125e40a7d45ae94e61e9f820384d49b170c2714b01e3 extends \XLite\Core\Templating\Twig\Template
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
";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Select\\Select2\\States", "labelHelp" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["The countries to which the selected states belong will be added to the zone automatically."]), "fieldName" => "zone_states", "label" => "States", "value" => $this->getAttribute($this->getAttribute(        // line 11
($context["this"] ?? null), "zone", []), "getZoneStates", [], "method"), "wrapperClass" => "zone-states"]]), "html", null, true);
        // line 12
        echo "
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/zones/details/parts/field.state.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 12,  34 => 11,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/zones/details/parts/field.state.twig", "");
    }
}
