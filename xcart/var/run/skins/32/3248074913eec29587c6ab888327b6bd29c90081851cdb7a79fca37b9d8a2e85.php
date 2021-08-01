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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XC/CanadaPost/products_return/parts/created.twig */
class __TwigTemplate_ff6366bb47d1d16a80383703e187f8f5cd833c4db55afe5d9555c2bd24a81439 extends \XLite\Core\Templating\Twig\Template
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
<p class=\"title\">
  ";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Created on"]), "html", null, true);
        echo " <span class=\"date\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCreateDate", [], "method"), "html", null, true);
        echo "</span> ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["by"]), "html", null, true);
        echo "
  ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "hasProfilePage", [], "method")) {
            // line 10
            echo "    <a class=\"name\" href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getProfileURL", [], "method"), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getProfileName", [], "method"), "html", null, true);
            echo "</a>
  ";
        }
        // line 12
        echo "  ";
        if ( !$this->getAttribute(($context["this"] ?? null), "hasProfilePage", [], "method")) {
            // line 13
            echo "    <span class=\"name\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getProfileName", [], "method"), "html", null, true);
            echo "</span>
  ";
        }
        // line 15
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "getMembership", [], "method")) {
            // line 16
            echo "    <span class=\"membership\">(";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "membership", []), "getName", [], "method"), "html", null, true);
            echo ")</span>
  ";
        }
        // line 18
        echo "</p>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XC/CanadaPost/products_return/parts/created.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  70 => 18,  64 => 16,  61 => 15,  55 => 13,  52 => 12,  44 => 10,  42 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XC/CanadaPost/products_return/parts/created.twig", "");
    }
}
