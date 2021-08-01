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

/* body.twig */
class __TwigTemplate_c6d5edfc92c276e4fe3ebca373b5743899e0e7f22fabf7fd105440e69d6a5641 extends \XLite\Core\Templating\Twig\Template
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
        echo "<!DOCTYPE html>
<html lang=\"";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "currentLanguage", []), "code", []), "html", null, true);
        echo "\" dir=\"";
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "currentLanguage", []), "r2l", [])) {
            echo "rtl";
        } else {
            echo "ltr";
        }
        echo "\"
  ";
        // line 6
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getHTMLAttributes", [], "method"));
        foreach ($context['_seq'] as $context["k"] => $context["v"]) {
            echo " ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["k"], "html", null, true);
            echo "=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["v"], "html", null, true);
            echo "\"";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['k'], $context['v'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        echo ">
";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Header"]]), "html", null, true);
        echo "
<body class=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getBodyClass", [], "method"), "html", null, true);
        echo "\">
<!--email_off-->
";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "body"]]), "html", null, true);
        echo "
<!--/email_off-->
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  67 => 10,  62 => 8,  58 => 7,  43 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/body.twig");
    }
}
