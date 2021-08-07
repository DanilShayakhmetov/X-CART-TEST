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

/* cache_management_actions/cell/action.twig */
class __TwigTemplate_8e0289ef6447499daaf37bf5979b8b68dfb13461e17dc7fcc2467770157fa5bf extends \XLite\Core\Templating\Twig\Template
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
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "column", []), "value", []), 1 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "entity", []), "viewParams", [])]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "cache_management_actions/cell/action.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 5,  30 => 4,);
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
 # Action button
 #}

{{ widget(this.column.value, this.entity.viewParams) }}", "cache_management_actions/cell/action.twig", "/mff/xcart/skins/admin/cache_management_actions/cell/action.twig");
    }
}
