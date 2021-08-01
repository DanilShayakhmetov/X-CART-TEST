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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/model/table/category/parts/info.products.twig */
class __TwigTemplate_c1893281aad7a5dd1f6c43066eccdd2b5a3cf91cdfe7fdff81f8908c796a65aa extends \XLite\Core\Templating\Twig\Template
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
<a href=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "category_products", "", ["id" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getCategoryId", [], "method")]]), "html", null, true);
        echo "\"class=\"count-link\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getProducts", [0 => null, 1 => true], "method"), "html", null, true);
        echo "</a>";
        ob_start(function () { return ''; });
        // line 8
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "hasSubcategories", [], "method")) {
            // line 9
            echo "(";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getProducts", [0 => $this->getAttribute(($context["this"] ?? null), "getProductsCountCondition", [], "method"), 1 => true], "method"), "html", null, true);
            echo ")
";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/model/table/category/parts/info.products.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 9,  39 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/model/table/category/parts/info.products.twig", "");
    }
}
