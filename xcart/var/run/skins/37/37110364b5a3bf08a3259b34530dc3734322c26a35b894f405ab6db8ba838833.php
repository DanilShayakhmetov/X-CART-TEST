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

/* subcategories/icons/body.twig */
class __TwigTemplate_3b943491052f2ede42c69de1e57efaa8e2f7b67b0de165d1b467874a40060502 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "getSubcategories", [], "method")) {
            // line 6
            echo "  <ul class=\"subcategory-view-icons subcategory-list grid-list clearfix\">
    ";
            // line 7
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getSubcategories", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["subcategory"]) {
                // line 8
                echo "    ";
                if ($this->getAttribute($context["subcategory"], "hasAvailableMembership", [], "method")) {
                    // line 9
                    echo "      <li>
        <a href=\"";
                    // line 10
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "category", "", ["category_id" => $this->getAttribute($context["subcategory"], "category_id", [])]]), "html", null, true);
                    echo "\">
          <span class=\"subcategory-icon\">
            ";
                    // line 12
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Image", "lazyLoad" => true, "image" => $this->getAttribute($context["subcategory"], "image", []), "maxWidth" => $this->getAttribute(($context["this"] ?? null), "getIconWidth", [], "method"), "maxHeight" => $this->getAttribute(($context["this"] ?? null), "getIconHeight", [], "method"), "centerImage" => "1", "alt" => $this->getAttribute(($context["this"] ?? null), "getAlt", [0 => $this->getAttribute($context["subcategory"], "image", [])], "method")]]), "html", null, true);
                    echo "
          </span>
          <span class=\"subcategory-name\">";
                    // line 14
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["subcategory"], "name", []), "html", null, true);
                    echo "</span>
        </a>
      </li>
    ";
                }
                // line 18
                echo "    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['subcategory'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getNestedViewList", [0 => "children"], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 20
                echo "      <li>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["item"], "display", [], "method"), "html", null, true);
                echo "</li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 22
            echo "  </ul>
";
        }
        // line 24
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "subcategories.base"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "subcategories/icons/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  89 => 24,  85 => 22,  76 => 20,  71 => 19,  65 => 18,  58 => 14,  53 => 12,  48 => 10,  45 => 9,  42 => 8,  38 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "subcategories/icons/body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/subcategories/icons/body.twig");
    }
}
