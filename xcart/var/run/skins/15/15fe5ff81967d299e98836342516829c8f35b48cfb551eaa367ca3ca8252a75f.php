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

/* main_center/page_container_parts/header_parts/logo.twig */
class __TwigTemplate_fde9d31cdda73e60773d4e27e261896feae3843e858ccdec54d29c607987b898 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"logo\"><a href=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context]), "html", null, true);
        echo "\">";
        echo $this->getAttribute(($context["this"] ?? null), "getSVGImage", [0 => "images/logo.svg"], "method");
        echo "</a></div>
";
    }

    public function getTemplateName()
    {
        return "main_center/page_container_parts/header_parts/logo.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  30 => 6,);
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
 # Main logo
 #
 # @ListChild (list=\"admin.main.page.header.left\", weight=\"100\")
 #}
<div class=\"logo\"><a href=\"{{ url() }}\">{{ this.getSVGImage('images/logo.svg')|raw }}</a></div>
", "main_center/page_container_parts/header_parts/logo.twig", "/mff/xcart/skins/admin/main_center/page_container_parts/header_parts/logo.twig");
    }
}
