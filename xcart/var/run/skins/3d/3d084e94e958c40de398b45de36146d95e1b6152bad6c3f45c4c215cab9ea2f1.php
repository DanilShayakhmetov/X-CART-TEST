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

/* /mff/xcart/skins/admin/zones/details/parts/field.zipcode.twig */
class __TwigTemplate_8ab562e3e07071e6c5a9343d3c21faf5696bf59eb83fa04c7fe22a08cbeeac8a extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Textarea\\Simple", "value" => $this->getAttribute($this->getAttribute(        // line 8
($context["this"] ?? null), "zone", []), "getZoneZipCodes", [0 => 1], "method"), "label" => "Zip/postal code masks", "labelHelp" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Zone zipcode masks help"]), "rows" => "5", "cols" => "70", "fieldName" => "zone_zipcodes"]]), "html", null, true);
        // line 13
        echo "
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/zones/details/parts/field.zipcode.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 13,  34 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/zones/details/parts/field.zipcode.twig", "");
    }
}
