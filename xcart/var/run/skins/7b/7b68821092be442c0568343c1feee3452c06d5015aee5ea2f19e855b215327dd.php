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

/* jscontainer/parts/js.twig */
class __TwigTemplate_0223a51cdaabbaacd6e28d0abed3ecf6188714bce02fb4ccf1bf28c9053820f8 extends \XLite\Core\Templating\Twig\Template
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
        if ( !$this->getAttribute(($context["this"] ?? null), "doJSAggregation", [], "method")) {
            // line 7
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getJSResources", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                // line 8
                echo "  <script src=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getResourceURL", [0 => $this->getAttribute($context["file"], "url", []), 1 => $context["file"]], "method"), "html", null, true);
                echo "\"
          ";
                // line 9
                if ($this->getAttribute($context["file"], "async", [])) {
                    echo " async";
                }
                // line 10
                echo "          ";
                if ($this->getAttribute($context["file"], "defer", [])) {
                    echo " defer";
                }
                // line 11
                echo "          ";
                if ($this->getAttribute($context["file"], "code", [])) {
                    echo " ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["file"], "code", []), "html", null, true);
                }
                echo "></script>
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    public function getTemplateName()
    {
        return "jscontainer/parts/js.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 11,  45 => 10,  41 => 9,  36 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "jscontainer/parts/js.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/jscontainer/parts/js.twig");
    }
}
