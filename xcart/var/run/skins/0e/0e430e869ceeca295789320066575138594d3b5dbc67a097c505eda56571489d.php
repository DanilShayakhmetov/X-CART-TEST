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

/* modules/XC/ProductComparison/header_indicator.twig */
class __TwigTemplate_d6a2586e17a2425db7cf6014bf96d0215e5ca466add87b13c9c5501d815a133e extends \XLite\Core\Templating\Twig\Template
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
        // line 1
        echo "<div class=\"header_product-comparison compare-indicator ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getIndicatorClasses", [], "method"), "html", null, true);
        echo "\">
\t<a ";
        // line 2
        if ( !$this->getAttribute(($context["this"] ?? null), "isDisabled", [], "method")) {
            echo "href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCompareURL", [], "method"), "html", null, true);
            echo "\" ";
        }
        echo " data-target=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCompareURL", [], "method"), "html", null, true);
        echo "\" title=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLinkHelpMessage", [], "method"), "html", null, true);
        echo "\">
\t\t<span class=\"counter\">";
        // line 3
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getComparedCount", [], "method"), "html", null, true);
        echo "</span>
\t</a>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/header_indicator.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 3,  35 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"header_product-comparison compare-indicator {{ this.getIndicatorClasses() }}\">
\t<a {% if not this.isDisabled() %}href=\"{{ this.getCompareURL() }}\" {% endif %} data-target=\"{{ this.getCompareURL() }}\" title=\"{{ this.getLinkHelpMessage() }}\">
\t\t<span class=\"counter\">{{this.getComparedCount()}}</span>
\t</a>
</div>
", "modules/XC/ProductComparison/header_indicator.twig", "/mff/xcart/skins/crisp_white/customer/modules/XC/ProductComparison/header_indicator.twig");
    }
}
