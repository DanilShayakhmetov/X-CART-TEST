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

/* /mff/xcart/skins/customer/items_list/product/parts/common.product-description.twig */
class __TwigTemplate_f67473795a1da39e53355dc3c3814370096d9067486f2f7e386434fdc96b5cf6 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getBriefDescription", [], "method")) {
            // line 7
            echo "  <div class=\"description product-description\">";
            echo $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getProcessedBriefDescription", [], "method");
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/items_list/product/parts/common.product-description.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/items_list/product/parts/common.product-description.twig", "");
    }
}
