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

/* items_list/model/table/order/cell.orderNumber_with_date.twig */
class __TwigTemplate_0fb433e88e42762f28b68baff9ce669cea0d1ae931cdb9631b86fae6b7c3b5ed extends \XLite\Core\Templating\Twig\Template
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
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table/field.twig");        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        // line 4
        $this->loadTemplate("items_list/model/table/field.twig", "items_list/model/table/order/cell.orderNumber_with_date.twig", 4)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 5
        echo "<span class=\"date\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatDate", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getDate", [], "method")], "method"), "html", null, true);
        echo ".</span>
<span class=\"time\">";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatDayTime", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "getDate", [], "method")], "method"), "html", null, true);
        echo "</span>
";
    }

    public function getTemplateName()
    {
        return "items_list/model/table/order/cell.orderNumber_with_date.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 6,  40 => 5,  35 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/model/table/order/cell.orderNumber_with_date.twig", "/mff/xcart/skins/admin/items_list/model/table/order/cell.orderNumber_with_date.twig");
    }
}
