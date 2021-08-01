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

/* /home/ruslan/Projects/next/output/xcart/src/skins/common/order/invoice/parts/items/item.name.twig */
class __TwigTemplate_f61534a0cc0ddc7b274942904cf667e4970d75ce1fe64fd0e075950467315c59 extends \XLite\Core\Templating\Twig\Template
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
        ob_start(function () { return ''; });
        // line 7
        echo "  <td class=\"item\">
    <div class=\"item-container\">
      <div class=\"item-image\">
        ";
        // line 10
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "hasImage", [], "method")) {
            // line 11
            echo "          <img src=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getResizedImageURL", [0 => 60, 1 => 60], "method"), "html", null, true);
            echo "\" alt=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getName", [], "method"), "html", null, true);
            echo "\">
        ";
        }
        // line 13
        echo "      </div>
      <div class=\"item-info\">
        ";
        // line 15
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getURL", [], "method")) {
            // line 16
            echo "          <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getURL", [], "method"), "html", null, true);
            echo "\" class=\"item-name\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getName", [], "method"), "html", null, true);
            echo "</a>
        ";
        }
        // line 18
        echo "        ";
        if ( !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getURL", [], "method")) {
            // line 19
            echo "          <span class=\"item-name\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getName", [], "method"), "html", null, true);
            echo "</span>
        ";
        }
        // line 21
        echo "        ";
        if ( !$this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "product", []), "isPersistent", [], "method")) {
            // line 22
            echo "          <span class=\"deleted-product-note\">(";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["deleted"]), "html", null, true);
            echo ")</span>
        ";
        }
        // line 24
        echo "
        ";
        // line 25
        if ($this->getAttribute(($context["this"] ?? null), "isViewListVisible", [0 => "invoice.item.name", 1 => ["item" => $this->getAttribute(($context["this"] ?? null), "item", [])]], "method")) {
            // line 26
            echo "          <ul class=\"subitem additional simple-list\">
          ";
            // line 27
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "invoice.item.name", "item" => $this->getAttribute(($context["this"] ?? null), "item", [])]]), "html", null, true);
            echo "
          </ul>
        ";
        }
        // line 30
        echo "      </div>
    </div>
  </td>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/common/order/invoice/parts/items/item.name.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 30,  87 => 27,  84 => 26,  82 => 25,  79 => 24,  73 => 22,  70 => 21,  64 => 19,  61 => 18,  53 => 16,  51 => 15,  47 => 13,  39 => 11,  37 => 10,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/common/order/invoice/parts/items/item.name.twig", "");
    }
}
