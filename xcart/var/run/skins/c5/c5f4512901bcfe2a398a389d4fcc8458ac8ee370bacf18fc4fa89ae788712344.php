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

/* layout/header/header_settings/login.twig */
class __TwigTemplate_2d4d49b0c9badb2a8714f731ba976e0cc615482049271ca54e553cce327623f5 extends \XLite\Core\Templating\Twig\Template
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
        // line 7
        echo "
";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "isAccountLinksVisible", [], "method")) {
            // line 9
            echo "<ul class=\"sign-in_block\">
\t<li class=\"account-link-sign_in\">
\t    ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\PopupLogin", "label" => "Sign in / sign up"]]), "html", null, true);
            echo "
\t</li>
</ul>
";
        }
    }

    public function getTemplateName()
    {
        return "layout/header/header_settings/login.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 11,  35 => 9,  33 => 8,  30 => 7,);
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
 # Log in link
 #
 # @ListChild (list=\"slidebar.navbar.account.second-additional-menu\", weight=\"400\")
 # @ListChild (list=\"layout.header.right.settings\", weight=\"400\")
 #}

{% if this.isAccountLinksVisible() %}
<ul class=\"sign-in_block\">
\t<li class=\"account-link-sign_in\">
\t    {{ widget('XLite\\\\View\\\\Button\\\\PopupLogin', label='Sign in / sign up') }}
\t</li>
</ul>
{% endif %}
", "layout/header/header_settings/login.twig", "/mff/xcart/skins/crisp_white/customer/layout/header/header_settings/login.twig");
    }
}
