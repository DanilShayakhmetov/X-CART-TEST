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

/* modules/creator/wishlist/wishlist_table/body.twig */
class __TwigTemplate_b85599caf829cd63b8936f0d015ae8ef195ee50670bb8c951ed62979963348b1 extends \XLite\Core\Templating\Twig\Template
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
<div id=\"compare\">
  ";
        // line 6
        if (($this->getAttribute(($context["this"] ?? null), "getProductsCount", [], "method") > 0)) {
            // line 7
            echo "    <table class=\"comparison-table\">
      <tbody class=\"header\">
      ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "wishlist_table.header"]]), "html", null, true);
            echo "
      </tbody>
      <tbody class=\"header-hidden\">
      <tr>
        <td style=\"";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStyle", [], "method"), "html", null, true);
            echo "\">&nbsp;</td>
        ";
            // line 14
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProducts", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
                // line 15
                echo "          <td style=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStyle", [], "method"), "html", null, true);
                echo "\">&nbsp;</td>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 17
            echo "      </tr>
      </tbody>
      <tbody class=\"header-fixed\">
      ";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "wishlist_table.header_fixed"]]), "html", null, true);
            echo "
      </tbody>
      <tbody class=\"data\">
      ";
            // line 23
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "wishlist_table.data"]]), "html", null, true);
            echo "
      </tbody>
    </table>
  ";
        } else {
            // line 27
            echo "    <span class=\"empty-notice\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["No products have been selected for comparison."]), "html", null, true);
            echo "</span>
  ";
        }
        // line 29
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "modules/creator/wishlist/wishlist_table/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 29,  82 => 27,  75 => 23,  69 => 20,  64 => 17,  55 => 15,  51 => 14,  47 => 13,  40 => 9,  36 => 7,  34 => 6,  30 => 4,);
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
 # Body
 #}

<div id=\"compare\">
  {% if this.getProductsCount() > 0 %}
    <table class=\"comparison-table\">
      <tbody class=\"header\">
      {{ widget_list('wishlist_table.header') }}
      </tbody>
      <tbody class=\"header-hidden\">
      <tr>
        <td style=\"{{ this.getStyle() }}\">&nbsp;</td>
        {% for product in this.getProducts() %}
          <td style=\"{{ this.getStyle() }}\">&nbsp;</td>
        {% endfor %}
      </tr>
      </tbody>
      <tbody class=\"header-fixed\">
      {{ widget_list('wishlist_table.header_fixed') }}
      </tbody>
      <tbody class=\"data\">
      {{ widget_list('wishlist_table.data') }}
      </tbody>
    </table>
  {% else %}
    <span class=\"empty-notice\">{{ t('No products have been selected for comparison.') }}</span>
  {% endif %}
</div>
", "modules/creator/wishlist/wishlist_table/body.twig", "/mff/xcart/skins/customer/modules/creator/wishlist/wishlist_table/body.twig");
    }
}
