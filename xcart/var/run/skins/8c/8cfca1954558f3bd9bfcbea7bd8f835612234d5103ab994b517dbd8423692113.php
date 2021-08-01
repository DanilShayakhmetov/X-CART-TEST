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

/* footer/right/social_link.twig */
class __TwigTemplate_e210c2a42a5459023579e24b31c4411221f72e46a21aae4a6ba095c11e3de92e extends \XLite\Core\Templating\Twig\Template
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
        if ( !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "auth", []), "isAdmin", [], "method")) {
            // line 8
            echo "  <div class=\"social-links\">
    <span>";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Find us on"]), "html", null, true);
            echo "</span>
    <a href=\"http://www.facebook.com/xcart/\" class=\"facebook\" target=\"_blank\" title=\"";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Facebook"]), "html", null, true);
            echo "\"><i class=\"fa fa-facebook-square\"></i></a>
    <a href=\"https://twitter.com/x_cart\" class=\"twitter\" target=\"_blank\" title=\"";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Twitter"]), "html", null, true);
            echo "\"><i class=\"fa fa-twitter-square\"></i></a>
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "footer/right/social_link.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 11,  42 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "footer/right/social_link.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/footer/right/social_link.twig");
    }
}
