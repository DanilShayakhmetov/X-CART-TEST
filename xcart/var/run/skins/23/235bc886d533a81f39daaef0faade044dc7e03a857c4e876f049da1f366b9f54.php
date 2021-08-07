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

/* contact_us/parts/contact_us.address.twig */
class __TwigTemplate_14f38ccb3a5b324ff0eb255f8ef9a0a78b42ff66bffffb7c3cb49cfc7abe9eed extends \XLite\Core\Templating\Twig\Template
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
<ul class=\"contact_us-address_links\">
 \t";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "getLocation", [], "method")) {
            // line 9
            echo "\t\t<li class=\"location\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLocation", [], "method"), "html", null, true);
            echo "</li>
\t";
        }
        // line 11
        echo " \t";
        if ($this->getAttribute(($context["this"] ?? null), "getPhone", [], "method")) {
            // line 12
            echo "\t\t<li class=\"phone\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPhone", [], "method"), "html", null, true);
            echo "</li>
\t";
        }
        // line 14
        echo " \t";
        if ($this->getAttribute(($context["this"] ?? null), "getEmail", [], "method")) {
            // line 15
            echo "\t\t<li class=\"email\"><a href=\"mailto:";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEmail", [], "method"), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEmail", [], "method"), "html", null, true);
            echo "</a></li>
\t";
        }
        // line 17
        echo "  ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "contact_us.parts.last"]]), "html", null, true);
        echo "
</ul>";
    }

    public function getTemplateName()
    {
        return "contact_us/parts/contact_us.address.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 17,  54 => 15,  51 => 14,  45 => 12,  42 => 11,  36 => 9,  34 => 8,  30 => 6,);
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
 # Contact us address links
 #
 # @ListChild (list=\"contact_us.parts\", weight=\"10\")
 #}

<ul class=\"contact_us-address_links\">
 \t{% if this.getLocation() %}
\t\t<li class=\"location\">{{ this.getLocation() }}</li>
\t{% endif %}
 \t{% if this.getPhone() %}
\t\t<li class=\"phone\">{{ this.getPhone() }}</li>
\t{% endif %}
 \t{% if this.getEmail() %}
\t\t<li class=\"email\"><a href=\"mailto:{{ this.getEmail() }}\">{{ this.getEmail() }}</a></li>
\t{% endif %}
  {{ widget_list('contact_us.parts.last') }}
</ul>", "contact_us/parts/contact_us.address.twig", "/mff/xcart/skins/crisp_white/customer/contact_us/parts/contact_us.address.twig");
    }
}
