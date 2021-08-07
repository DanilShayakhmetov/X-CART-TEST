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

/* /mff/xcart/skins/customer/modules/XC/Reviews/review/parts/field.reviewer_name.twig */
class __TwigTemplate_9a1cba62e98853f46b9aa3d7631fcee80fdb874aa14ef26724423cf834c78604 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"form-item clearfix\">
  ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\FormField\\Input\\Text", "required" => "true", "placeholder" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Full name", [], null, "placeholder"]), "fieldName" => "reviewerName", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Your name"]), "value" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "review", []), "reviewerName", [])]]), "html", null, true);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XC/Reviews/review/parts/field.reviewer_name.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/XC/Reviews/review/parts/field.reviewer_name.twig", "");
    }
}
