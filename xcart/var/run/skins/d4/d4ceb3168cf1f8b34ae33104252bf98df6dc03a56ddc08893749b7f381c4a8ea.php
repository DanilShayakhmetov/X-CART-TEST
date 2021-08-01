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

/* layout/footer/powered_by.twig */
class __TwigTemplate_7da17e5a2cce5ecadf314d4e21b2739dbac9434c385b56c72d1f3b0dde2ada11 extends \XLite\Core\Templating\Twig\Template
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
        // line 4
        echo "
";
        // line 6
        echo "
<div class=\"powered-by\">
  <p class=\"copyright\">&copy; ";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCompanyYear", [], "method"), "html", null, true);
        echo " ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_name", []), "html", null, true);
        echo ". ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["All rights reserved."]), "html", null, true);
        echo "</p>
  <p class=\"powered-by-label\">";
        // line 9
        echo $this->getAttribute(($context["this"] ?? null), "getMessage", [], "method");
        echo "</p>
</div>
";
    }

    public function getTemplateName()
    {
        return "layout/footer/powered_by.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 9,  37 => 8,  33 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "layout/footer/powered_by.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/layout/footer/powered_by.twig");
    }
}
