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

/* items_list/product/parts/common.product-thumbnail.twig */
class __TwigTemplate_cff0b7ff3f20dcfaa374ed8099b286e2a0e94e05281cb65eda459f5485eb07dc extends \XLite\Core\Templating\Twig\Template
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
        return "items_list/product/parts/common.product-thumbnail.twig";
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
        return new Source("{##
 # Item thumbnail
 #
 # @ListChild (list=\"itemsList.product.grid.customer.info.photo\", weight=\"10\")
 # @ListChild (list=\"itemsList.product.small_thumbnails.customer.info.photo\", weight=\"10\")
 # @ListChild (list=\"itemsList.product.big_thumbnails.customer.info.photo\", weight=\"10\")
 # @ListChild (list=\"productBlock.info.photo\", weight=\"100\")
 #}
<a
  href=\"{{ this.getProductURL(this.categoryId) }}\"
  class=\"product-thumbnail\">
  {{ widget('\\\\XLite\\\\View\\\\Image', lazyLoad=true, image=this.product.getImage(), maxWidth=this.getIconWidth(), maxHeight=this.getIconHeight(), alt=this.getIconAlt(), className='photo') }}
</a>
", "items_list/product/parts/common.product-thumbnail.twig", "/mff/xcart/skins/customer/items_list/product/parts/common.product-thumbnail.twig");
    }
}
