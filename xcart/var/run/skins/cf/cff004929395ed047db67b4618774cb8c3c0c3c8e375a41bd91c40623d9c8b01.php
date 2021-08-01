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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/parts/common.sort-options.twig */
class __TwigTemplate_3ebb1edad69cd1fbda5bb762ec65896df8615837846cecf0db9303788db7e97d extends \XLite\Core\Templating\Twig\Template
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
        // line 8
        echo "
";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isSortBySelectorVisible", [], "method")) {
            // line 10
            echo "  <div class=\"sort-box\">
  
    <ul class=\"display-sort sort-crit grid-list\" id=\"";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSortWidgetId", [0 => $this->getAttribute(($context["this"] ?? null), "true", [])], "method"), "html", null, true);
            echo "\">
    ";
            // line 13
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "sortByModes", []));
            foreach ($context['_seq'] as $context["key"] => $context["name"]) {
                // line 14
                echo "    <li class=\"list-type-grid ";
                if ($this->getAttribute(($context["this"] ?? null), "isSortByModeSelected", [0 => $context["key"]], "method")) {
                    echo " selected";
                }
                echo "\">
      <a data-sort-by=\"";
                // line 15
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "\" data-sort-order=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSortOrderToChange", [0 => $context["key"]], "method"), "html", null, true);
                echo "\" href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getActionURL", [0 => ["sortBy" => $context["key"], "sortOrder" => $this->getAttribute(($context["this"] ?? null), "getSortOrderToChange", [0 => $context["key"]], "method")]], "method"), "html", null, true);
                echo "\">
        ";
                // line 16
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [$context["name"]]), "html", null, true);
                echo "<i class=\"sort-arrow ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getSortArrowClassCSS", [0 => $context["key"]], "method"), "html", null, true);
                echo "\"></i>
      </a>
    </li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['name'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            echo "    </ul>
  
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/parts/common.sort-options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 20,  62 => 16,  54 => 15,  47 => 14,  43 => 13,  39 => 12,  35 => 10,  33 => 9,  30 => 8,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/items_list/product/parts/common.sort-options.twig", "");
    }
}
