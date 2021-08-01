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

/* modules/XC/ProductComparison/compare/products/body.twig */
class __TwigTemplate_0303cd20fc0b03e379439571effef867f781a6606bd5c8220af65c901beae7d7 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"add-to-compare products compare-checkbox\">
    <input id=\"";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCheckboxId", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "product_id", [])], "method"), "html", null, true);
        echo "\" type=\"checkbox\" data-id=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "product_id", []), "html", null, true);
        echo "\"";
        if ($this->getAttribute(($context["this"] ?? null), "isChecked", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "product_id", [])], "method")) {
            echo " checked=\"checked\"";
        }
        echo " />
    <label for=\"";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCheckboxId", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "product_id", [])], "method"), "html", null, true);
        echo "\" title=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Add to compare"]), "html", null, true);
        echo "\"></label>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/compare/products/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/ProductComparison/compare/products/body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/modules/XC/ProductComparison/compare/products/body.twig");
    }
}
