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

/* items_list/product/center/grid/body-css-layout.twig */
class __TwigTemplate_25152c25102bc34afd4c4e41ccc42bf6e36de2f3e15cbec21872631932f5dddd extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "itemsList.product.cart"]]), "html", null, true);
        echo "

<div class=\"products\">

  ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "getPageData", [], "method")) {
            // line 10
            echo "    <ul class=\"products-grid grid-list\">
      ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getPageData", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 12
                echo "        <li class=\"product-cell box-product\">
    
          ";
                // line 14
                echo $this->getAttribute(($context["this"] ?? null), "getProductWidgetContent", [0 => $context["product"]], "method");
                echo "
    
        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 18
            echo "      ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getNestedViewList", [0 => "items"], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 19
                echo "        <li class=\"product-cell box-product\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "display", [], "method"), "html", null, true);
                echo "</li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "    </ul>
  ";
        }
        // line 23
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "items_list/product/center/grid/body-css-layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  81 => 23,  77 => 21,  68 => 19,  63 => 18,  53 => 14,  49 => 12,  45 => 11,  42 => 10,  40 => 9,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/product/center/grid/body-css-layout.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/center/grid/body-css-layout.twig");
    }
}
