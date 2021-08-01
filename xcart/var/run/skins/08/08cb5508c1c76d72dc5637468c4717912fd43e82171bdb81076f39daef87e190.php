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
class __TwigTemplate_e02cbab9b9072d1930da653ea7dc620bfb3fa67654ffbb4728d952c1c498826f extends \XLite\Core\Templating\Twig\Template
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
        return new Source("", "modules/CDev/GoogleAnalytics/impression-block.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/CDev/GoogleAnalytics/impression-block.twig");
    }
}
