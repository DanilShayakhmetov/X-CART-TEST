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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/VendorMessages/order/list/parts/spec.messages.twig */
class __TwigTemplate_2ecc5f1932fb200f386d9dba2e6bc2d32a82237843a64d06f00d5daf6f2a3d38 extends \XLite\Core\Templating\Twig\Template
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
        echo "<li class=\"messages-link ";
        if ($this->getAttribute(($context["this"] ?? null), "countUnreadMessages", [0 => $this->getAttribute(($context["this"] ?? null), "order", [])], "method")) {
            echo "unread";
        } else {
            echo "read";
        }
        echo "\">
    <a href=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "order_messages", "", ["order_number" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "order", []), "order_number", [])]]), "html", null, true);
        echo "\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displaySVGImage", [0 => "modules/XC/VendorMessages/images/mail.svg"], "method"), "html", null, true);
        echo "<span>";
        if ($this->getAttribute(($context["this"] ?? null), "countUnreadMessages", [0 => $this->getAttribute(($context["this"] ?? null), "order", [])], "method")) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["X unread messages", ["count" => $this->getAttribute(($context["this"] ?? null), "countUnreadMessages", [0 => $this->getAttribute(($context["this"] ?? null), "order", [])], "method")]]), "html", null, true);
        } else {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Contact seller"]), "html", null, true);
        }
        echo "</span></a>
</li>";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/VendorMessages/order/list/parts/spec.messages.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/VendorMessages/order/list/parts/spec.messages.twig", "");
    }
}
