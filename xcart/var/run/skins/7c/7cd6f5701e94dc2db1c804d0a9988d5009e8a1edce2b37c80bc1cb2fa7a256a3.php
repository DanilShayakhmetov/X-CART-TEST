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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/mini_cart/horizontal/parts/checkout.twig */
class __TwigTemplate_0f0fca8fa6a141ecd6218029d1825f2b4007ae91edab7de5507ab1a23681fe77 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "checkCart", [], "method")) {
            // line 7
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\SimpleLink", "label" => "Checkout", "location" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "checkout"]), "style" => "regular-main-button checkout"]]), "html", null, true);
            echo "
";
        }
        // line 9
        if ( !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "checkCart", [], "method")) {
            // line 10
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\SimpleLink", "label" => "Checkout", "location" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "checkout"]), "style" => "regular-main-button checkout", "disabled" => "true"]]), "html", null, true);
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/mini_cart/horizontal/parts/checkout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 10,  38 => 9,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/mini_cart/horizontal/parts/checkout.twig", "");
    }
}
