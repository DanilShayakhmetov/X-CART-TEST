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

/* /mff/xcart/skins/admin/items_list/model/table/order_item/restore.twig */
class __TwigTemplate_1113c470c3a16bb0bdd6551ce14855a37f11c0caf64b594baa178b28d45471b4 extends \XLite\Core\Templating\Twig\Template
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
        // line 7
        echo "
<div class=\"restore-orig-price\" data-orig-price=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getOriginalPrice", [0 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method"), "html", null, true);
        echo "\">
  <span title=\"";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Current price for the selected configuration and quantity: X", ["price" => $this->getAttribute(($context["this"] ?? null), "formatPrice", [0 => $this->getAttribute(($context["this"] ?? null), "getOriginalPrice", [0 => $this->getAttribute(($context["this"] ?? null), "entity", [])], "method"), 1 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "currency", [])], "method")]]), "html", null, true);
        echo "\"></span>
  <input type=\"hidden\" name=\"auto[items][";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "item_id", []), "html", null, true);
        echo "][price]\" ";
        if ($this->getAttribute(($context["this"] ?? null), "isAutoItem", [0 => $this->getAttribute(($context["this"] ?? null), "entity", []), 1 => "price"], "method")) {
            echo "value=\"1\"";
        } else {
            echo "value=\"\"";
        }
        echo " />
</div>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/items_list/model/table/order_item/restore.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 10,  37 => 9,  33 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/items_list/model/table/order_item/restore.twig", "");
    }
}
