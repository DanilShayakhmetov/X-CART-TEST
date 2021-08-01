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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/News/page/news_message/parts/link.twig */
class __TwigTemplate_73714b2bee8dd1f8f98ce8c9add23a2b4c0e24a733eb32b5cc3009d20a729316 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"archive-link\">
  ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "getPreviousURL", [0 => $this->getAttribute(($context["this"] ?? null), "newsMessage", [])], "method")) {
            // line 9
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPreviousURL", [0 => $this->getAttribute(($context["this"] ?? null), "newsMessage", [])], "method"), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Previous news"]), "html", null, true);
            echo "</a>
  ";
        }
        // line 11
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getNextURL", [0 => $this->getAttribute(($context["this"] ?? null), "newsMessage", [])], "method")) {
            // line 12
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getNextURL", [0 => $this->getAttribute(($context["this"] ?? null), "newsMessage", [])], "method"), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Next news"]), "html", null, true);
            echo "</a>
  ";
        }
        // line 14
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/News/page/news_message/parts/link.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 14,  47 => 12,  44 => 11,  36 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/News/page/news_message/parts/link.twig", "");
    }
}
