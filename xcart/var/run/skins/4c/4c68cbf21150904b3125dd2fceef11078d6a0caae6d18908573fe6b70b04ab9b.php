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

/* modules/XC/ThemeTweaker/themetweaker/custom_css/css.twig */
class __TwigTemplate_c0e58139a0ba736ecd88a094ccbe4d9fe58ec5be03c25612b01b36af3328ac16 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isInCustomCssMode", [], "method")) {
            // line 8
            echo "
    ";
            // line 9
            if ($this->getAttribute(($context["this"] ?? null), "isCustomCssEnabled", [], "method")) {
                // line 10
                echo "        <style rel=\"stylesheet\" media=\"screen\" type=\"text/css\" data-custom-css>
        ";
                // line 11
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCustomCssText", [], "method"), "html", null, true);
                echo "
        </style>
    ";
            } else {
                // line 14
                echo "        <script type=\"text/css\" data-custom-css>
        ";
                // line 15
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCustomCssText", [], "method"), "html", null, true);
                echo "
        </script>
    ";
            }
        }
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker/custom_css/css.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 15,  49 => 14,  43 => 11,  40 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/ThemeTweaker/themetweaker/custom_css/css.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker/custom_css/css.twig");
    }
}
