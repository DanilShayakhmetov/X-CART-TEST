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

/* layout/header/header.right.mobile.minicart.twig */
class __TwigTemplate_29ad9d0b0e4f09bf7c7e1d3ac06054c4e08f851218613fda34995a79b19b3529 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"lc-minicart-placeholder\"></div>
";
    }

    public function getTemplateName()
    {
        return "layout/header/header.right.mobile.minicart.twig";
    }

    public function getDebugInfo()
    {
        return array (  30 => 6,);
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
 # Header bar search box
 #
 # @ListChild (list=\"layout.header.right.mobile\", weight=\"100\")
 #}

<div class=\"lc-minicart-placeholder\"></div>
", "layout/header/header.right.mobile.minicart.twig", "/mff/xcart/skins/crisp_white/customer/layout/header/header.right.mobile.minicart.twig");
    }
}
