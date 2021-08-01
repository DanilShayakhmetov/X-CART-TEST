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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/page/parts/items/item.name.twig */
class __TwigTemplate_0511d727bb910ace92f6897071eaa284d5fa7b4d9f9255a49f7eca01bd13354d extends \XLite\Core\Templating\Twig\Template
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
  ";
        // line 17
        if ($this->getAttribute(($context["this"] ?? null), "isViewListVisible", [0 => "invoice.item.name", 1 => ["item" => $this->getAttribute(($context["this"] ?? null), "item", [])]], "method")) {
            // line 18
            echo "    <ul class=\"subitem additional simple-list\">
    ";
            // line 19
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "invoice.item.name", "item" => $this->getAttribute(($context["this"] ?? null), "item", [])]]), "html", null, true);
            echo "
    </ul>
  ";
        }
        // line 22
        echo "</td>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/page/parts/items/item.name.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 22,  69 => 19,  66 => 18,  64 => 17,  61 => 16,  55 => 14,  52 => 13,  46 => 11,  43 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/page/parts/items/item.name.twig", "");
    }
}
