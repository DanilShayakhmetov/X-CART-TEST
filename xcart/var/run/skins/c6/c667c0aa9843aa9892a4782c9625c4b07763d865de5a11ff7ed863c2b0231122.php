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

/* /mff/xcart/skins/customer/modules/creator/wishlist/wishlist_table/parts/buttons.twig */
class __TwigTemplate_4925d24ed9a55041bfd1979b7a50818e81bf7c073ebb01df8a22de91fd83c06f extends \XLite\Core\Templating\Twig\Template
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
<tr class=\"buttons\">
  <td>
    ";
        // line 9
        if (($this->getAttribute(($context["this"] ?? null), "getProductsCount", [], "method") > 1)) {
            // line 10
            echo "      <input type=\"checkbox\" id=\"diff\" /><label for=\"diff\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Differences only"]), "html", null, true);
            echo "</label>
    ";
        }
        // line 12
        echo "  </td>
  ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 14
            echo "    <td  class=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["product"], "getAdditionalClass", [], "method"), "html", null, true);
            echo "\" >
      ";
            // line 15
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getProductButtonWidget", [0 => $context["product"]], "method"), "html", null, true);
            echo "
    </td>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        echo "</tr>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/creator/wishlist/wishlist_table/parts/buttons.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 18,  55 => 15,  50 => 14,  46 => 13,  43 => 12,  37 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/creator/wishlist/wishlist_table/parts/buttons.twig", "");
    }
}
