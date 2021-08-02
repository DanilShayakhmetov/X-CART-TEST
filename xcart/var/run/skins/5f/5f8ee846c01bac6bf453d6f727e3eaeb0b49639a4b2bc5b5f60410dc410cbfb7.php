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

/* items_list/model/table.twig */
class __TwigTemplate_75f075916917385d53d212a1df2d9b79c1e5bc5f41ca7373761cd9b1a5d77cc8 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isSearchVisible", [], "method")) {
            // line 6
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => $this->getAttribute(($context["this"] ?? null), "getSearchPanelClass", [], "method"), "itemsList" => $this->getAttribute(($context["this"] ?? null), "getItemsListObject", [], "method")]]), "html", null, true);
            echo "
";
        }
        // line 8
        echo "
";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isWrapWithForm", [], "method")) {
            // line 10
            echo "
  ";
            // line 11
            $this->startForm($this->getAttribute($this->getAttribute(($context["this"] ?? null), "formOptions", []), "class", []), ["name" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "formOptions", []), "name", []), "formTarget" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "formOptions", []), "target", []), "formAction" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "formOptions", []), "action", []), "formParams" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "formOptions", []), "params", []), "confirmRemove" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "formOptions", []), "confirmRemove", [])]);            // line 12
            echo "
    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table_content.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            // line 13
            $this->loadTemplate("items_list/model/table_content.twig", "items_list/model/table.twig", 13)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 14
            echo "
  ";
            $this->endForm();            // line 16
            echo "
";
        } else {
            // line 18
            echo "
  ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table_content.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            // line 19
            $this->loadTemplate("items_list/model/table_content.twig", "items_list/model/table.twig", 19)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 20
            echo "
";
        }
        // line 22
        echo "
";
    }

    public function getTemplateName()
    {
        return "items_list/model/table.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 22,  83 => 20,  78 => 19,  70 => 18,  66 => 16,  63 => 14,  58 => 13,  50 => 12,  49 => 11,  46 => 10,  44 => 9,  41 => 8,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/model/table.twig", "/mff/xcart/skins/admin/items_list/model/table.twig");
    }
}
