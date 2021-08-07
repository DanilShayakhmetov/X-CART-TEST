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

/* modules/CDev/GoogleAnalytics/impression-block.twig */
class __TwigTemplate_fbdd0018b6b39b145f83218cf719e74901262c5bd50ac6b94b9fc524b7459c45 extends \XLite\Core\Templating\Twig\Template
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
        // line 13
        echo "
";
        // line 14
        if ($this->getAttribute(($context["this"] ?? null), "shouldRegisterImpression", [], "method")) {
            // line 15
            echo "    <script data-ga-ec-action=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getImpressionData", [], "method"), "html", null, true);
            echo "\"></script>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/CDev/GoogleAnalytics/impression-block.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 15,  33 => 14,  30 => 13,);
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
 # Impression block
 #
 # @ListChild (list=\"itemsList.product.grid.customer.info\", weight=\"20\")
 # @ListChild (list=\"itemsList.product.list.customer.info\", weight=\"20\")
 # @ListChild (list=\"itemsList.product.small_thumbnails.customer.details\", weight=\"20\")
 # @ListChild (list=\"itemsList.product.big_thumbnails.customer.info\", weight=\"200\")
 # @ListChild (list=\"itemsList.product.text_links.customer.info\", weight=\"100\")
 # @ListChild (list=\"itemsList.product.table.customer.add2cart\", weight=\"10\")
 # @ListChild (list=\"productBlock.info\", weight=\"200\")
 # @ListChild (list=\"itemsList.product.grid.customer.recently.info\", weight=\"10\")
 #}

{% if this.shouldRegisterImpression() %}
    <script data-ga-ec-action=\"{{ this.getImpressionData() }}\"></script>
{% endif %}
", "modules/CDev/GoogleAnalytics/impression-block.twig", "/mff/xcart/skins/customer/modules/CDev/GoogleAnalytics/impression-block.twig");
    }
}
