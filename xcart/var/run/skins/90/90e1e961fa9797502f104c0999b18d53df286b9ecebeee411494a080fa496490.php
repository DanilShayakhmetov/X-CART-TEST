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

/* modules/XC/ProductComparison/comparison_table/parts/images.twig */
class __TwigTemplate_d591ccf79acde2d09e82399050f46677c4d4a675d7e3e78e957457ca93f4888d extends \XLite\Core\Templating\Twig\Template
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
<tr class=\"images\">
  <td class=\"clear-list\">
    <a href=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "compare", "clear"]), "html", null, true);
        echo "\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Clear list"]), "html", null, true);
        echo "</a>
  </td>
  ";
        // line 11
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 12
            echo "    <td>
      <a target=\"_blank\"
        href=\"";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "product", "", ["product_id" => $this->getAttribute($context["product"], "product_id", [])]]), "html", null, true);
            echo "\"
        class=\"product-thumbnail\">
          ";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Image", "image" => $this->getAttribute($context["product"], "getImage", [], "method"), "maxWidth" => "60", "maxHeight" => "60", "alt" => $this->getAttribute($context["product"], "name", []), "className" => "photo"]]), "html", null, true);
            echo "
      </a>
      <a
        href=\"";
            // line 19
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "compare", "delete", ["product_id" => $this->getAttribute($context["product"], "product_id", [])]]), "html", null, true);
            echo "\"
        class=\"remove\"
        title=\"";
            // line 21
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Remove"]), "html", null, true);
            echo "\"
        data-id=\"";
            // line 22
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["product"], "product_id", []), "html", null, true);
            echo "\">
        <img src=\"";
            // line 23
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
            echo "\" alt=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Remove"]), "html", null, true);
            echo "\" />
      </a>
    </td>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        echo "</tr>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/comparison_table/parts/images.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 27,  74 => 23,  70 => 22,  66 => 21,  61 => 19,  55 => 16,  50 => 14,  46 => 12,  42 => 11,  35 => 9,  30 => 6,);
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
 # Images
 #
 # @ListChild (list=\"comparison_table.header\", weight=\"100\")
 #}

<tr class=\"images\">
  <td class=\"clear-list\">
    <a href=\"{{ url('compare', 'clear') }}\">{{ t('Clear list') }}</a>
  </td>
  {% for product in this.getProducts() %}
    <td>
      <a target=\"_blank\"
        href=\"{{ url('product', '', {'product_id': product.product_id}) }}\"
        class=\"product-thumbnail\">
          {{ widget('\\\\XLite\\\\View\\\\Image', image=product.getImage(), maxWidth='60', maxHeight='60', alt=product.name, className='photo') }}
      </a>
      <a
        href=\"{{ url('compare', 'delete', {'product_id': product.product_id}) }}\"
        class=\"remove\"
        title=\"{{ t('Remove') }}\"
        data-id=\"{{ product.product_id }}\">
        <img src=\"{{ asset('images/spacer.gif') }}\" alt=\"{{ t('Remove') }}\" />
      </a>
    </td>
  {% endfor %}
</tr>
", "modules/XC/ProductComparison/comparison_table/parts/images.twig", "/mff/xcart/skins/customer/modules/XC/ProductComparison/comparison_table/parts/images.twig");
    }
}
