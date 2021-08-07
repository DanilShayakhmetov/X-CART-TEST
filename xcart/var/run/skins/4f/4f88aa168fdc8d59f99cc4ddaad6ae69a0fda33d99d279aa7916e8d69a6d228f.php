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

/* header/parts/favicon.twig */
class __TwigTemplate_fb8b563944e3c5fbe8beac0001fc9ac5876f6bc34dd4ae7aef81da33d208df8a extends \XLite\Core\Templating\Twig\Template
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
<link rel=\"shortcut icon\" href=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFavicon", [], "method"), "html", null, true);
        echo "\" type=\"image/x-icon\" />
<link rel=\"icon\"              sizes=\"192x192\"   href=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAppleIcon", [], "method"), "html", null, true);
        echo "\"/>
<link rel=\"apple-touch-icon\"  sizes=\"192x192\"   href=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAppleIcon", [], "method"), "html", null, true);
        echo "\">
";
    }

    public function getTemplateName()
    {
        return "header/parts/favicon.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 9,  37 => 8,  33 => 7,  30 => 6,);
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
 # Header part
 #
 # @ListChild (list=\"head\", weight=\"80\")
 #}

<link rel=\"shortcut icon\" href=\"{{ this.getFavicon() }}\" type=\"image/x-icon\" />
<link rel=\"icon\"              sizes=\"192x192\"   href=\"{{ this.getAppleIcon() }}\"/>
<link rel=\"apple-touch-icon\"  sizes=\"192x192\"   href=\"{{ this.getAppleIcon() }}\">
", "header/parts/favicon.twig", "/mff/xcart/skins/admin/header/parts/favicon.twig");
    }
}
