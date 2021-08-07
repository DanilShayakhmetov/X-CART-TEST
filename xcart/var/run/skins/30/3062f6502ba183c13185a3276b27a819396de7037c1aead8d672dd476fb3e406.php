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

/* /mff/xcart/skins/crisp_white/customer/product/details/parts/common.loupe.twig */
class __TwigTemplate_9d0fab8d5f0e831d844157afd67cdf17741c3ea589217b33dd956d3e17862dbf extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isLoupeVisible", [], "method")) {
            // line 8
            echo "  <a href=\"javascript:void(0);\" class=\"loupe\">
    ";
            // line 9
            echo $this->getAttribute(($context["this"] ?? null), "getSVGImage", [0 => "images/zoom.svg"], "method");
            echo "
  </a>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/crisp_white/customer/product/details/parts/common.loupe.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/crisp_white/customer/product/details/parts/common.loupe.twig", "");
    }
}
