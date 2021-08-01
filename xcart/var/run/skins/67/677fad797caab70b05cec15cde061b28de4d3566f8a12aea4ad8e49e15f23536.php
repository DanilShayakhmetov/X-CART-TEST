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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/header/parts/parts.css/css_aggregation.twig */
class __TwigTemplate_d4e23b041410880d701878ebdd28cc9f21f7f8d82a9030ead5d6d36369fcd36d extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "doCSSAggregation", [], "method")) {
            // line 7
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getAggregateCSSResources", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 8
                echo "  ";
                if (($this->getAttribute(($context["this"] ?? null), "doCSSOptimization", [], "method") && $this->getAttribute(($context["this"] ?? null), "isResourceSuitableForOptimization", [0 => $context["file"]], "method"))) {
                    // line 9
                    echo "    ";
                    echo $this->getAttribute(($context["this"] ?? null), "getInternalCssByResource", [0 => $context["file"]], "method");
                    echo "
  ";
                } else {
                    // line 11
                    echo "    ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, ["template" => "header/parts/parts.css/css_resource.twig", "async" => $this->getAttribute(                    // line 12
$context["file"], "async", []), "media" => $this->getAttribute(                    // line 13
$context["file"], "media", []), "url" => $this->getAttribute(                    // line 14
($context["this"] ?? null), "getResourceURL", [0 => $this->getAttribute($context["file"], "url", []), 1 => $context["file"]], "method")]]), "html", null, true);
                    // line 15
                    echo "
  ";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/header/parts/parts.css/css_aggregation.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 15,  49 => 14,  48 => 13,  47 => 12,  45 => 11,  39 => 9,  36 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/header/parts/parts.css/css_aggregation.twig", "");
    }
}
