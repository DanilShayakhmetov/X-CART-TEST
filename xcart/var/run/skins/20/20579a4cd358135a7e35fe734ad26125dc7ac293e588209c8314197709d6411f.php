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

/* /mff/xcart/skins/common/order/invoice/parts/head.address.twig */
class __TwigTemplate_eed6c337a7104c9437f37543e5777a7ae74bd735b56d1fcea07fdbfac069209a extends \XLite\Core\Templating\Twig\Template
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
        $context["company"] = $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getCompanyConfiguration", [], "method");
        // line 8
        echo "
<td class=\"right-adresses-part address\">
  ";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "invoice.subhead.companyAddress", "company" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getCompanyConfiguration", [], "method")]]), "html", null, true);
        echo "
</td>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/common/order/invoice/parts/head.address.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/common/order/invoice/parts/head.address.twig", "");
    }
}
