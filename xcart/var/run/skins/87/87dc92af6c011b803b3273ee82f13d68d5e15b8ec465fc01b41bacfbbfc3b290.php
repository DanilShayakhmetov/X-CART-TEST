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

/* /mff/xcart/skins/admin/modules/CDev/PINCodes/product/auto_generate.twig */
class __TwigTemplate_e878838130067e6350147489f1400c17a1beb55ef65f5241b0368098eb13fec8 extends \XLite\Core\Templating\Twig\Template
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
<li class=\"pin-codes-auto\">
  ";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\CDev\\PINCodes\\View\\FormField\\Select\\AutoPinCodes", "value" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getAutoPinCodes", [], "method"), "fieldName" => "autoPinCodes", "label" => "PIN codes generation method", "help" => (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "getPinCodesEnabled", [], "method")) ? ("") : (call_user_func_array($this->env->getFunction('t')->getCallable(), ["You will be able to add pin codes after saving changes."])))]]), "html", null, true);
        echo "
</li>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/CDev/PINCodes/product/auto_generate.twig";
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
        return new Source("", "/mff/xcart/skins/admin/modules/CDev/PINCodes/product/auto_generate.twig", "");
    }
}
