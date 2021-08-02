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

/* shopping_cart/parts/box.estimator.method.twig */
class __TwigTemplate_68595dc0d5fd6848e36ff8bc916b9d57db80947f3fb14b214685b68f63cd48e7 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"selected-shipping-method\">
  <span class=\"method-name\">";
        // line 5
        echo $this->getAttribute(($context["this"] ?? null), "getName", [], "method");
        echo "</span>
  <span class=\"shipping-cost\">(";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCost", [], "method"), "html", null, true);
        echo ")</span>
</div>
";
    }

    public function getTemplateName()
    {
        return "shopping_cart/parts/box.estimator.method.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "shopping_cart/parts/box.estimator.method.twig", "/mff/xcart/skins/customer/shopping_cart/parts/box.estimator.method.twig");
    }
}
