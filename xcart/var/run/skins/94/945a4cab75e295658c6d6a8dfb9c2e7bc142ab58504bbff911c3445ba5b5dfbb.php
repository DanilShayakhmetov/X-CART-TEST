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

/* /mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/product/details/parts/common.only_registered.twig */
class __TwigTemplate_58ea98dbf8f5d094aecb0732eff7251fe7e545c2d07467ec6b255289003bb152 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "isNotAllowedXpaymentsSubscription", [], "method")) {
            // line 8
            echo "  <span class=\"xps-only-registered-label\">
    ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["This product is available only for registered users"]), "html", null, true);
            echo "
  </span>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/product/details/parts/common.only_registered.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 9,  32 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/product/details/parts/common.only_registered.twig", "");
    }
}
