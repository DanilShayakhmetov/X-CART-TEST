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

/* common/surcharge_parts/surcharge.twig */
class __TwigTemplate_bd2cb50b79d7c5dc77f04672c17af5937c631b3ec73ed4e692e3acc3b6f8684f extends \XLite\Core\Templating\Twig\Template
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
<span class=\"surcharge-cell\">";
        // line 7
        echo $this->getAttribute(($context["this"] ?? null), "formatPriceHTML", [0 => $this->getAttribute(($context["this"] ?? null), "getSurcharge", [], "method"), 1 => $this->getAttribute(($context["this"] ?? null), "getCurrency", [], "method"), 2 => 1], "method");
        echo "</span>
";
    }

    public function getTemplateName()
    {
        return "common/surcharge_parts/surcharge.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "common/surcharge_parts/surcharge.twig", "/mff/xcart/skins/customer/common/surcharge_parts/surcharge.twig");
    }
}
