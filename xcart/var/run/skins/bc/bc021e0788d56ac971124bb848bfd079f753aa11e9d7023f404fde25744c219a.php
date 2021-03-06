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

/* form_field/../modules/XC/ThemeTweaker/form_field/code_mirror/body.twig */
class __TwigTemplate_356de3087af1bc868f81bc3c3b09e7912b59fce2ec0ce3bbf71a87b0a9af2732 extends \XLite\Core\Templating\Twig\Template
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
<span class=\"input-field-wrapper ";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getWrapperClass", [], "method"), "html", null, true);
        echo "\" v-pre=\"v-pre\">
    <textarea ";
        // line 6
        echo $this->getAttribute(($context["this"] ?? null), "getAttributesCode", [], "method");
        echo ">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getValue", [], "method"), "html", null, true);
        echo "</textarea>
</span>";
    }

    public function getTemplateName()
    {
        return "form_field/../modules/XC/ThemeTweaker/form_field/code_mirror/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 6,  33 => 5,  30 => 4,);
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
 # Code mirror textarea field
 #}

<span class=\"input-field-wrapper {{ this.getWrapperClass() }}\" v-pre=\"v-pre\">
    <textarea {{ this.getAttributesCode()|raw }}>{{ this.getValue() }}</textarea>
</span>", "form_field/../modules/XC/ThemeTweaker/form_field/code_mirror/body.twig", "/mff/xcart/skins/admin/modules/XC/ThemeTweaker/form_field/code_mirror/body.twig");
    }
}
