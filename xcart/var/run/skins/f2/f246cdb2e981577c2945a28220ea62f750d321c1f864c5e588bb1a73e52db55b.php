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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/footer/parts/css_aggregation.twig */
class __TwigTemplate_9667e7d730af3cffe8b391d3c98565b21b653d3ef9932693cd4dcde3500ed29a extends \XLite\Core\Templating\Twig\Template
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
        if (($this->getAttribute(($context["this"] ?? null), "doCSSAggregation", [], "method") && $this->getAttribute(($context["this"] ?? null), "doCSSOptimization", [], "method"))) {
            // line 8
            echo "  ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getAggregateCSSResources", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 9
                echo "    ";
                if ($this->getAttribute(($context["this"] ?? null), "isResourceSuitableForOptimization", [0 => $context["file"]], "method")) {
                    // line 10
                    echo "    <script>
      var xhr = new XMLHttpRequest();
      xhr.open('GET', '";
                    // line 12
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getResourceURL", [0 => $this->getAttribute($context["file"], "url", []), 1 => $context["file"]], "method"), "html", null, true);
                    echo "');
      xhr.send('');
    </script>
    ";
                }
                // line 16
                echo "  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/footer/parts/css_aggregation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 16,  47 => 12,  43 => 10,  40 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/footer/parts/css_aggregation.twig", "");
    }
}
