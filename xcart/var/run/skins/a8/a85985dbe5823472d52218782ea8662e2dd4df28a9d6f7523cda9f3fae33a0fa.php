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

/* /mff/xcart/skins/admin/modules/XC/CanadaPost/products_return/parts/form.action.left.status.twig */
class __TwigTemplate_5e9e2a357c9d37172452f353fc1c1b9e651faf66f8ff50a65ddd6826f68b055b extends \XLite\Core\Templating\Twig\Template
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
<div class=\"status\">
  ";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\CanadaPost\\View\\FormField\\Select\\ReturnStatus", "label" => "Status", "fieldName" => "postedData[status]", "value" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "productsReturn", []), "getStatus", [], "method")]]), "html", null, true);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/XC/CanadaPost/products_return/parts/form.action.left.status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/XC/CanadaPost/products_return/parts/form.action.left.status.twig", "");
    }
}
