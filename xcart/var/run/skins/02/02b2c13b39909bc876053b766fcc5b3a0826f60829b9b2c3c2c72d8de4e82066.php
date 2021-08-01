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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/QSL/CloudSearch/cloud_filters/items_list/product/parts/common.filters.twig */
class __TwigTemplate_6cce5e7aa883108d6cc64a5ac4b973588970b53fea1b6bf5be09b59db537eabe extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isCloudFiltersMobileLinkVisible", [], "method")) {
            // line 8
            echo "    <div id=\"cloud-filters-mobile-link\" class=\"hidden\">
        <a href=\"#cf-slide-menu\">
            ";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Filter"]), "html", null, true);
            echo "
            ";
            // line 11
            if (($this->getAttribute(($context["this"] ?? null), "cloudFiltersCount", []) > 0)) {
                echo " (";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "cloudFiltersCount", []), "html", null, true);
                echo ")";
            }
            // line 12
            echo "            »
        </a>
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/QSL/CloudSearch/cloud_filters/items_list/product/parts/common.filters.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  49 => 12,  43 => 11,  39 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/QSL/CloudSearch/cloud_filters/items_list/product/parts/common.filters.twig", "");
    }
}
