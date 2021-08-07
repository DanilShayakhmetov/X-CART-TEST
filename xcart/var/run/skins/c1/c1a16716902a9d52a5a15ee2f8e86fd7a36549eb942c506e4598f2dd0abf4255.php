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

/* mini_cart/horizontal/parts/item.image.twig */
class __TwigTemplate_2ad0e6bafbe614cc3a34c4d5c92796ad2a615068361804053015f3aa810b881f extends \XLite\Core\Templating\Twig\Template
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
        echo "
";
        // line 7
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "hasImage", [], "method")) {
            // line 8
            echo "\t<span class=\"item-image\">
\t";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Image", "lazyLoad" => true, "image" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getImage", [], "method"), "maxWidth" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getMiniCartImageWidth", [], "method"), "maxHeight" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getMiniCartImageHeight", [], "method"), "centerImage" => "1", "alt" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getName", [], "method")]]), "html", null, true);
            echo "
\t</span>
";
        }
    }

    public function getTemplateName()
    {
        return "mini_cart/horizontal/parts/item.image.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
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
 # Display horizontal minicart item name
 #
 # @ListChild (list=\"minicart.horizontal.item\", weight=\"5\")
 #}

{% if this.item.hasImage() %}
\t<span class=\"item-image\">
\t{{ widget('\\\\XLite\\\\View\\\\Image', lazyLoad=true, image=this.item.getImage(), maxWidth=this.item.getMiniCartImageWidth(), maxHeight=this.item.getMiniCartImageHeight(), centerImage='1', alt=this.item.getName()) }}
\t</span>
{% endif %}", "mini_cart/horizontal/parts/item.image.twig", "/mff/xcart/skins/crisp_white/customer/mini_cart/horizontal/parts/item.image.twig");
    }
}
