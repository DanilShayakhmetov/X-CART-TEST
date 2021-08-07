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
class __TwigTemplate_82c27b8613892c5fe5b7624f958dbb158a1f4daf9f3b993b499731daf5879f41 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "currentLanguage", []), "getCode", [], "method"), "html", null, true);
        echo "\"";
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
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Header"]]), "html", null, true);
        echo "
<body ";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "getBodyClass", [], "method")) {
            echo "class=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getBodyClass", [], "method"), "html", null, true);
            echo "\"";
        }
        echo ">
";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "body_top"]]), "html", null, true);
        echo "
<!--email_off-->
";
        // line 10
        $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getCommonJSData", [], "method")], "method");
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "body"]]), "html", null, true);
        echo "
";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Footer"]]), "html", null, true);
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
        return array (  73 => 15,  69 => 11,  67 => 10,  62 => 8,  54 => 7,  50 => 6,  33 => 5,  30 => 4,);
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
 # Common layout
 #}
<!DOCTYPE html>
<html lang=\"{{ this.currentLanguage.getCode() }}\"{% for k, v in this.getHTMLAttributes() %} {{ k }}=\"{{ v }}\"{% endfor %}>
  {{ widget('\\\\XLite\\\\View\\\\Header') }}
<body {% if this.getBodyClass() %}class=\"{{ this.getBodyClass() }}\"{% endif %}>
{{ widget_list('body_top') }}
<!--email_off-->
{% do this.displayCommentedData(this.getCommonJSData()) %}
{{ widget_list('body') }}
{##
 # Please note that any custom list child of 'body' will NOT have its CSS/JS resources loaded because the resources block is being 'body' child itself. Use 'layout.main' or 'layout.footer' instead.
 #}
{{ widget('\\\\XLite\\\\View\\\\Footer') }}
<!--/email_off-->
</body>
</html>
", "body.twig", "/mff/xcart/skins/customer/body.twig");
    }
}
