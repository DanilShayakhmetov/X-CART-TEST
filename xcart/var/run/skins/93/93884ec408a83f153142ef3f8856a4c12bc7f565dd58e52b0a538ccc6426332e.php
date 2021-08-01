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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/body/items/item.name.twig */
class __TwigTemplate_6e9166297699ca7101814b5c5123479fa8818ab1b1d17d59f63675c2949657c0 extends \XLite\Core\Templating\Twig\Template
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
        echo "<td class=\"item\">
  ";
        // line 7
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getURL", [], "method")) {
            // line 8
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getURL", [], "method"), "html", null, true);
            echo "\" class=\"item-name\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getName", [], "method"), "html", null, true);
            echo "</a>
  ";
        }
        // line 10
        echo "  ";
        if ( !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getURL", [], "method")) {
            // line 11
            echo "    <span class=\"item-name\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getName", [], "method"), "html", null, true);
            echo "</span>
  ";
        }
        // line 13
        echo "  ";
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "product", []), "isPersistent", [], "method")) {
            // line 14
            echo "    <span class=\"deleted-product-note\">(";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["deleted"]), "html", null, true);
            echo ")</span>
  ";
        }
        // line 16
        echo "
  <div class=\"item-options\">
    ";
        // line 18
        if ($this->getAttribute(($context["this"] ?? null), "isViewListVisible", [0 => "packing_slip.item.name", 1 => ["item" => $this->getAttribute(($context["this"] ?? null), "item", [])]], "method")) {
            // line 19
            echo "      <ul class=\"subitem additional simple-list\">
        ";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "packing_slip.item.name", "item" => $this->getAttribute(($context["this"] ?? null), "item", [])]]), "html", null, true);
            echo "
      </ul>
    ";
        }
        // line 23
        echo "    ";
        if ($this->getAttribute(($context["this"] ?? null), "isAttributesVisible", [0 => $this->getAttribute(($context["this"] ?? null), "item", [])], "method")) {
            // line 24
            echo "      <ul class=\"subitem attributes simple-list\">
        <li>";
            // line 25
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Options:"]), "html", null, true);
            echo "&nbsp;</li>
        ";
            // line 26
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getAttributeValues", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["av"]) {
                // line 27
                echo "          <li class=\"attribute\">
            ";
                // line 28
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["av"], "getName", [], "method"), "html", null, true);
                echo ": ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["av"], "value", []), "html", null, true);
                echo "
          </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['av'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 31
            echo "      </ul>
    ";
        }
        // line 33
        echo "  </div>
</td>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/body/items/item.name.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  108 => 33,  104 => 31,  93 => 28,  90 => 27,  86 => 26,  82 => 25,  79 => 24,  76 => 23,  70 => 20,  67 => 19,  65 => 18,  61 => 16,  55 => 14,  52 => 13,  46 => 11,  43 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/body/items/item.name.twig", "");
    }
}
