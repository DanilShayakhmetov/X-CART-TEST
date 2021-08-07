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

/* header/parts/preloaded_labels.twig */
class __TwigTemplate_ac3c686f4f3afd81891f4002c9a01ad31958cf3f129dc72ccbb7b6acf30eb499 extends \XLite\Core\Templating\Twig\Template
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
<script type=\"text/javascript\">
  window.xlite_preloaded_labels =";
        // line 8
        echo $this->getAttribute(($context["this"] ?? null), "getPreloadedLabelsJSON", [], "method");
        echo ";
</script>
";
    }

    public function getTemplateName()
    {
        return "header/parts/preloaded_labels.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 8,  30 => 6,);
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
 # Head list children
 #
 # @ListChild (list=\"head\", weight=\"1500\")
 #}

<script type=\"text/javascript\">
  window.xlite_preloaded_labels ={{ this.getPreloadedLabelsJSON()|raw }};
</script>
", "header/parts/preloaded_labels.twig", "/mff/xcart/skins/admin/header/parts/preloaded_labels.twig");
    }
}
