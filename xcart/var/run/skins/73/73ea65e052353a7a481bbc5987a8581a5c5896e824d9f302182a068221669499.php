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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/states/header.twig */
class __TwigTemplate_e1fe617962c6b447312503cf0457023f35e135ec3a885e38757f32e342a27959 extends \XLite\Core\Templating\Twig\Template
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
<ul class=\"states-list\">
  ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getDisplayedCountries", [], "method"));
        foreach ($context['_seq'] as $context["countryCode"] => $context["countryName"]) {
            // line 9
            echo "    <li>
      ";
            // line 10
            if (($context["countryCode"] != $this->getAttribute(($context["this"] ?? null), "getCountryCode", [], "method"))) {
                // line 11
                echo "        <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "states", "", ["country_code" => $context["countryCode"]]]), "html", null, true);
                echo "\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["countryName"], "html", null, true);
                echo "</a>
      ";
            } else {
                // line 13
                echo "        <span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["countryName"], "html", null, true);
                echo "</span>
      ";
            }
            // line 15
            echo "    </li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['countryCode'], $context['countryName'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/states/header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 17,  57 => 15,  51 => 13,  43 => 11,  41 => 10,  38 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/states/header.twig", "");
    }
}
