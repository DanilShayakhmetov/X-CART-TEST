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

/* /home/ruslan/Projects/next/output/xcart/src/skins/mail/common/order/invoice/parts/title.twig */
class __TwigTemplate_d45047da6fa9dc576f52cf112cab8976a2d4ad9c4e47f94c9df1d982930f9b98 extends \XLite\Core\Templating\Twig\Template
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
        echo "<td>
    <h2 class=\"invoice\">
        <a href=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getOrderCustomerUrl", [], "method"), "html", null, true);
        echo "\">
            ";
        // line 9
        echo $this->getAttribute(($context["this"] ?? null), "getInvoiceTitle", [], "method");
        echo "
        </a>
    </h2>
    <div class=\"subhead\">
        <div class=\"subhead-item order-date\">
            ";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "formatTime", [0 => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "getDate", [], "method")], "method"), "html", null, true);
        echo "
        </div>
        <div class=\"subhead-item grand-total grand-total-head\">
            ";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Grand total"]), "html", null, true);
        echo ": ";
        echo $this->getAttribute(($context["this"] ?? null), "getOrderTotal", [], "method");
        echo "
        </div>
    </div>
</td>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/mail/common/order/invoice/parts/title.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 17,  46 => 14,  38 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/mail/common/order/invoice/parts/title.twig", "");
    }
}
