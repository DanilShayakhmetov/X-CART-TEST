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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/address/order/parts/info.twig */
class __TwigTemplate_6132a63f178940b3a47210f94a2dc86cefccd5041f816f8e66665c51919a1e9d extends \XLite\Core\Templating\Twig\Template
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
<div class=\"info\">
  ";
        // line 8
        if (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "origProfile", []) && $this->getAttribute(($context["this"] ?? null), "isShowProfileLink", [], "method"))) {
            // line 9
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "profile", "", ["profile_id" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "origProfile", []), "profileId", [])]]), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "</a>
  ";
        } else {
            // line 11
            echo "    ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "
  ";
        }
        // line 13
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isShowEmail", [], "method")) {
            // line 14
            echo "    <span class=\"separator\">-</span>
    ";
            // line 16
            echo "    <span class=\"email\">
    ";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Inline\\Input\\Text\\Email", "entity" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getProfile", [], "method"), "name" => "address_login", "fieldName" => "login", "fieldNamespace" => "address_login"]]), "html", null, true);
            echo "
    </span>
  ";
        }
        // line 20
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/address/order/parts/info.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 20,  59 => 17,  56 => 16,  53 => 14,  50 => 13,  44 => 11,  36 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/address/order/parts/info.twig", "");
    }
}
