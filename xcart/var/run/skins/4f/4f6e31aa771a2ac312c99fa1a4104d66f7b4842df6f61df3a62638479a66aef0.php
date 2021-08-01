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

/* modules/CDev/SimpleCMS/primary_menu_items.twig */
class __TwigTemplate_601dbbccde36e969160bc794ad8f84cf1ab963bc9b26f6dcefdee0323d3ff6d6 extends \XLite\Core\Templating\Twig\Template
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
        if ( !$this->getAttribute(($context["this"] ?? null), "isSlidebar", [], "method")) {
            // line 6
            echo "<ul class=\"nav navbar-nav top-main-menu\">
";
        }
        // line 8
        echo "  ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header.flycategories"]]), "html", null, true);
        echo "
  ";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getItems", [], "method"));
        foreach ($context['_seq'] as $context["i"] => $context["item"]) {
            // line 10
            echo "    ";
            if ($this->getAttribute(($context["this"] ?? null), "isLevelUp", [0 => $this->getAttribute($context["item"], "depth", [])], "method")) {
                // line 11
                echo "  <ul>
    ";
            } else {
                // line 13
                echo "      ";
                if ( !$this->getAttribute(($context["this"] ?? null), "isFirstElement", [], "method")) {
                    // line 14
                    echo "    </li>
      ";
                }
                // line 16
                echo "    ";
            }
            // line 17
            echo "
    ";
            // line 18
            echo $this->getAttribute(($context["this"] ?? null), "closeMenuList", [0 => $this->getAttribute($context["item"], "depth", [])], "method");
            echo "
    <li ";
            // line 19
            echo $this->getAttribute(($context["this"] ?? null), "displayItemClass", [0 => $context["i"], 1 => $context["item"]], "method");
            echo ">
    ";
            // line 20
            if ($this->getAttribute($context["item"], "url", [])) {
                // line 21
                echo "      <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "url", []), "html", null, true);
                echo "\" ";
                if ($this->getAttribute($context["item"], "active", [])) {
                    echo "class=\"active\"";
                }
                echo "><span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "label", []), "html", null, true);
                echo "</span></a>
    ";
            } else {
                // line 23
                echo "      <span class=\"primary-title\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "label", []), "html", null, true);
                echo "</span>
    ";
            }
            // line 25
            echo "  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['i'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 26
        echo "    </li>
  ";
        // line 27
        echo $this->getAttribute(($context["this"] ?? null), "closeMenuList", [], "method");
        echo "
";
        // line 28
        if ( !$this->getAttribute(($context["this"] ?? null), "isSlidebar", [], "method")) {
            // line 29
            echo "</ul>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/CDev/SimpleCMS/primary_menu_items.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 29,  109 => 28,  105 => 27,  102 => 26,  96 => 25,  90 => 23,  78 => 21,  76 => 20,  72 => 19,  68 => 18,  65 => 17,  62 => 16,  58 => 14,  55 => 13,  51 => 11,  48 => 10,  44 => 9,  39 => 8,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/CDev/SimpleCMS/primary_menu_items.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/CDev/SimpleCMS/primary_menu_items.twig");
    }
}
