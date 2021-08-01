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

/* layout/header/navbar.desktop.twig */
class __TwigTemplate_efe4d0a87c6e462c684b1fec2d2ba5e7c0d545d018b728045b4ca12139965099 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"navbar navbar-inverse mobile-hidden\" role=\"navigation\" data-desktop-navbar>
    ";
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "shouldRender", [], "method")) {
            // line 7
            echo "    <div class=\"collapse navbar-collapse\">
      ";
            // line 8
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header.menu"]]), "html", null, true);
            echo "
      ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header.menu.after"]]), "html", null, true);
            echo "
    </div>
    ";
        }
        // line 12
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "layout/header/navbar.desktop.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  49 => 12,  43 => 9,  39 => 8,  36 => 7,  34 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "layout/header/navbar.desktop.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/layout/header/navbar.desktop.twig");
    }
}
