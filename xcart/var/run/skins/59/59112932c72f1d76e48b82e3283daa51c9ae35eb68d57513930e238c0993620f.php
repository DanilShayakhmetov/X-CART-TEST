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

/* modules/QSL/CloudSearch/cloud_filters/slidebar.twig */
class __TwigTemplate_8f9e1cd1cec5d9d520acdc3cd14f8f247ffdb4a5cab405fe557fe62a11f336bd extends \XLite\Core\Templating\Twig\Template
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
        // line 4
        echo "
<nav id=\"cf-slide-menu\" class=\"hidden\">
    ";
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "shouldRender", [], "method")) {
            // line 7
            echo "        <div id=\"cloud-filters-mobile\" class=\"cloud-filters\" v-cloak>
            <h4 class=\"title\">";
            // line 8
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Filters"]), "html", null, true);
            echo "</h4>

            ";
            // line 10
            echo call_user_func_array($this->env->getFunction('include')->getCallable(), [$this->env, $context, "modules/QSL/CloudSearch/cloud_filters/sidebar_box/body.twig"]);
            echo "
        </div>
    ";
        }
        // line 13
        echo "</nav>";
    }

    public function getTemplateName()
    {
        return "modules/QSL/CloudSearch/cloud_filters/slidebar.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 13,  44 => 10,  39 => 8,  36 => 7,  34 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/QSL/CloudSearch/cloud_filters/slidebar.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/QSL/CloudSearch/cloud_filters/slidebar.twig");
    }
}
