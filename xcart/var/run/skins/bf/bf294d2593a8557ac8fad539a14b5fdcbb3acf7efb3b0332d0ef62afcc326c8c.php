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

/* modules/XC/VendorMessages/mobile_header_parts/navbar/account/messages.twig */
class __TwigTemplate_5a71681ff1559e1d8e846c8c2daebb4b8442046ed6550812c148604e1d805e37 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isLogged", [], "method")) {
            // line 8
            echo "<li>
\t<a class=\"icon-altmail\" href=\"";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "messages", ""]), "html", null, true);
            echo "\">
\t\t<span>";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Messages"]), "html", null, true);
            echo "</span>
\t</a>
</li>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/XC/VendorMessages/mobile_header_parts/navbar/account/messages.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  42 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
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
 # Messages link
 #
 # @ListChild (list=\"slidebar.navbar.account\", weight=\"50\")
 #}

{% if this.isLogged() %}
<li>
\t<a class=\"icon-altmail\" href=\"{{ url('messages', '') }}\">
\t\t<span>{{ t('Messages') }}</span>
\t</a>
</li>
{% endif %}", "modules/XC/VendorMessages/mobile_header_parts/navbar/account/messages.twig", "/mff/xcart/skins/crisp_white/customer/modules/XC/VendorMessages/mobile_header_parts/navbar/account/messages.twig");
    }
}
