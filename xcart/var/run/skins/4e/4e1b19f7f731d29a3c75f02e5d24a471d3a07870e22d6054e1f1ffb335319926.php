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

/* header/parts/parts.css/fonts_preload.twig */
class __TwigTemplate_c31c49401ef3e276442a9010e757b2273d818806b3bd22faba9b485ddc6d2bb6 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "doCSSAggregation", [], "method")) {
            // line 10
            echo "  <link rel=\"preload\" as=\"font\" type=\"font/woff2\" href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "layout", []), "getResourceWebPath", [0 => "css/fonts/fontawesome-webfont.woff2", 1 => "url", 2 => "common"], "method"), "html", null, true);
            echo "?v=4.6.3\" crossorigin />
  <link rel=\"preload\" as=\"font\" type=\"font/ttf\" href=\"";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["iconfont/xcartskin.ttf"]), "html", null, true);
            echo "?oaqn6v\" crossorigin />
";
        }
    }

    public function getTemplateName()
    {
        return "header/parts/parts.css/fonts_preload.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 11,  35 => 10,  33 => 9,  30 => 8,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "header/parts/parts.css/fonts_preload.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/header/parts/parts.css/fonts_preload.twig");
    }
}
