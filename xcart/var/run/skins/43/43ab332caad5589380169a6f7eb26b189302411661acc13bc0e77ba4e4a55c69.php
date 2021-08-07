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

/* mini_cart/horizontal/parts/item.attribute_values.twig */
class __TwigTemplate_47a04f8ea4d697c85847149c35a6aa8d38a00b30f7704b651a03389089766307 extends \XLite\Core\Templating\Twig\Template
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
<a class=\"item-attribute-values underline-emulation\" id=\"item-attribute";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getItemId", [], "method"), "html", null, true);
        echo "\" data-rel=\"div.item-attribute-values.item-";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getItemId", [], "method"), "html", null, true);
        echo "\">
  <span>";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["attributes"]), "html", null, true);
        echo "</span>
</a>
<div class=\"internal-popup item-attribute-values item-";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getItemId", [], "method"), "html", null, true);
        echo "\" style=\"display: none;\">
  <ul class=\"item-attribute-values\">
    ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getSortedAttributeValues", [0 => $this->getAttribute(($context["this"] ?? null), "getMaxAttributesCount", [], "method")], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["av"]) {
            // line 11
            echo "      <li>";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["av"], "getActualName", [], "method"), "html", null, true);
            echo ": ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["av"], "getActualValue", [], "method"), "html", null, true);
            echo "</li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['av'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "  </ul>
  ";
        // line 14
        if ($this->getAttribute(($context["this"] ?? null), "needMoreAttributesLink", [0 => $this->getAttribute(($context["this"] ?? null), "item", [])], "method")) {
            // line 15
            echo "    <div class=\"more-attributes\">
        <a href=\"";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "cart"]), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["More attributes"]), "html", null, true);
            echo "</a>
    </div>
  ";
        }
        // line 19
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "mini_cart/horizontal/parts/item.attribute_values.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 19,  72 => 16,  69 => 15,  67 => 14,  64 => 13,  53 => 11,  49 => 10,  44 => 8,  39 => 6,  33 => 5,  30 => 4,);
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
 # Minicart row with item attribute-values
 #}

<a class=\"item-attribute-values underline-emulation\" id=\"item-attribute{{ this.item.getItemId() }}\" data-rel=\"div.item-attribute-values.item-{{ this.item.getItemId() }}\">
  <span>{{ t('attributes') }}</span>
</a>
<div class=\"internal-popup item-attribute-values item-{{ this.item.getItemId() }}\" style=\"display: none;\">
  <ul class=\"item-attribute-values\">
    {% for av in this.item.getSortedAttributeValues(this.getMaxAttributesCount()) %}
      <li>{{ av.getActualName() }}: {{ av.getActualValue() }}</li>
    {% endfor %}
  </ul>
  {% if this.needMoreAttributesLink(this.item) %}
    <div class=\"more-attributes\">
        <a href=\"{{ url('cart') }}\">{{ t('More attributes') }}</a>
    </div>
  {% endif %}
</div>
", "mini_cart/horizontal/parts/item.attribute_values.twig", "/mff/xcart/skins/crisp_white/customer/mini_cart/horizontal/parts/item.attribute_values.twig");
    }
}
