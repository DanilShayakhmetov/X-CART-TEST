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

/* layout/content/category_description.twig */
class __TwigTemplate_501a69338642c22cd8af23eb0db3fc5b0a830a5c0644a0bad2bac3d9fb1f3164 extends \XLite\Core\Templating\Twig\Template
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
        if (($this->getAttribute(($context["this"] ?? null), "getDescription", [], "method") || $this->getAttribute(($context["this"] ?? null), "isInInlineEditorMode", [], "method"))) {
            // line 6
            echo "<div class=\"category-description\" ";
            echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "getCategory", [], "method"), "getFieldMetadata", [0 => "description"], "method")], "method");
            echo ">";
            echo $this->getAttribute(($context["this"] ?? null), "getDescription", [], "method");
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "layout/content/category_description.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "layout/content/category_description.twig", "/mff/xcart/skins/customer/layout/content/category_description.twig");
    }
}
