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

/* modules/CDev/Sale/top_categories/additional_links.twig */
class __TwigTemplate_226f4660c932d71fb75d956713a90fa7733e2c1c21d237fa208f88c2ea0da3ea extends \XLite\Core\Templating\Twig\Template
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
";
        // line 5
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getSaleDiscounts", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["discount"]) {
            // line 6
            echo "  <li>
    <a href=\"";
            // line 7
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSalePageLink", [0 => $context["discount"]], "method"), "html", null, true);
            echo "\" ";
            if (($this->getAttribute(($context["this"] ?? null), "getCurrentDiscountId", [], "method") == $this->getAttribute($context["discount"], "getId", [], "method"))) {
                echo "class=\"active\"";
            }
            echo ">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["discount"], "getName", [], "method"), "html", null, true);
            echo "</a>
  </li>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['discount'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "modules/CDev/Sale/top_categories/additional_links.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 7,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/CDev/Sale/top_categories/additional_links.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/CDev/Sale/top_categories/additional_links.twig");
    }
}
