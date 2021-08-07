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

/* /mff/xcart/skins/admin/items_list/product/modify/common/parts/columns/checkbox.twig */
class __TwigTemplate_31c2fdf2ccd0964393e6b46cef9735e317a04b0f92d0f0aa62f34c02379a6ec5 extends \XLite\Core\Templating\Twig\Template
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
<td class=\"checkbox-column\"><input type=\"checkbox\" class=\"checkbox ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getProductId", [], "method"), "html", null, true);
        echo "\" value=\"1\" name=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getNameToDelete", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getProductId", [], "method")], "method"), "html", null, true);
        echo "\" /></td>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/items_list/product/modify/common/parts/columns/checkbox.twig";
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
        return new Source("", "/mff/xcart/skins/admin/items_list/product/modify/common/parts/columns/checkbox.twig", "");
    }
}
