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

/* banner_rotation/body.twig */
class __TwigTemplate_589cdfb9c61014283d239792d040313eb3c9ebeae844846206d9d0c998081208 extends \XLite\Core\Templating\Twig\Template
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
<div id=\"banner-rotation-widget\" class=\"carousel slide banner-carousel\">
  ";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getCommentedData", [], "method")], "method"), "html", null, true);
        echo "

  <!-- Indicators -->
  ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isRotationEnabled", [], "method")) {
            // line 10
            echo "    <ol class=\"carousel-indicators\">
      ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getImages", [], "method"));
            foreach ($context['_seq'] as $context["i"] => $context["image"]) {
                // line 12
                echo "        <li data-target=\"#banner-rotation-widget\" data-slide-to=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["i"], "html", null, true);
                echo "\"></li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['i'], $context['image'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 14
            echo "    </ol>
  ";
        }
        // line 16
        echo "
  <div class=\"carousel-inner not-initialized\" role=\"listbox\">
    ";
        // line 18
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getImages", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["image"]) {
            // line 19
            echo "      <a ";
            if ($this->getAttribute($this->getAttribute($context["image"], "bannerRotationSlide", []), "getFrontLink", [], "method")) {
                echo " href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["image"], "bannerRotationSlide", []), "getFrontLink", [], "method"), "html", null, true);
                echo "\"";
            }
            echo " class=\"item\">
        ";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Image", "lazyLoad" => true, "image" => $context["image"], "alt" => $this->getAttribute($context["image"], "getAlt", [], "method"), "resizeImage" => false, "useCache" => false]]), "html", null, true);
            echo "
      </a>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['image'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo "  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "banner_rotation/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 23,  79 => 20,  70 => 19,  66 => 18,  62 => 16,  58 => 14,  49 => 12,  45 => 11,  42 => 10,  40 => 9,  34 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "banner_rotation/body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/banner_rotation/body.twig");
    }
}
