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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/parts/common.product-thumbnail.twig */
class __TwigTemplate_d5548f91536f75b117276208b58d279393728f821d018905d90b77d33204060e extends \XLite\Core\Templating\Twig\Template
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
        // line 9
        echo "<a
  href=\"";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getProductURL", [0 => $this->getAttribute(($context["this"] ?? null), "categoryId", [])], "method"), "html", null, true);
        echo "\"
  class=\"product-thumbnail\">
  ";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Image", "lazyLoad" => true, "image" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getImage", [], "method"), "maxWidth" => $this->getAttribute(($context["this"] ?? null), "getIconWidth", [], "method"), "maxHeight" => $this->getAttribute(($context["this"] ?? null), "getIconHeight", [], "method"), "alt" => $this->getAttribute(($context["this"] ?? null), "getIconAlt", [], "method"), "className" => "photo"]]), "html", null, true);
        echo "
</a>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/parts/common.product-thumbnail.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 12,  33 => 10,  30 => 9,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/parts/common.product-thumbnail.twig", "");
    }
}
