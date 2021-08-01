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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/header/parts/link_favicon.twig */
class __TwigTemplate_6d632a67a6fce75e97242e7b45849941ab2ffc6e2a1c8b88b177f4e81d28c480 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "displayFavicon", [], "method")) {
            // line 8
            echo "  <link rel=\"shortcut icon\" href=\"";
            echo $this->getAttribute(($context["this"] ?? null), "getFavicon", [], "method");
            echo "\" type=\"image/x-icon\" />
";
        }
        // line 10
        echo "
<link rel=\"icon\"              sizes=\"192x192\"   href=\"";
        // line 11
        echo $this->getAttribute(($context["this"] ?? null), "getAppleIcon", [], "method");
        echo "\"/>
<link rel=\"apple-touch-icon\"  sizes=\"192x192\"   href=\"";
        // line 12
        echo $this->getAttribute(($context["this"] ?? null), "getAppleIcon", [], "method");
        echo "\">
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/header/parts/link_favicon.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 12,  44 => 11,  41 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/header/parts/link_favicon.twig", "");
    }
}
