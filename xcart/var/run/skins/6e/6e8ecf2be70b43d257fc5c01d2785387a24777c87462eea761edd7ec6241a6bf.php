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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/items_list/payment/methods/parts/action.right.after.twig */
class __TwigTemplate_07471542d335384ae907647210aec28ded7edb44b453aa16ff1c1a3f9f035f2a extends \XLite\Core\Templating\Twig\Template
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
        // line 8
        echo "
";
        // line 9
        if (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getReferralPageURL", [], "method") || $this->getAttribute(($context["this"] ?? null), "getKnowledgeBasePageURL", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method"))) {
            // line 10
            echo "  <div class=\"learn-more\">
    ";
            // line 11
            if ($this->getAttribute(($context["this"] ?? null), "getKnowledgeBasePageURL", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
                // line 12
                echo "      <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getKnowledgeBasePageURL", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method"), "html", null, true);
                echo "\" target=\"_blank\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Learn More"]), "html", null, true);
                echo "</a>
    ";
            }
            // line 14
            echo "    ";
            if (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getReferralPageURL", [], "method") && $this->getAttribute(($context["this"] ?? null), "getKnowledgeBasePageURL", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method"))) {
                // line 15
                echo "      <span>&amp;</span>
    ";
            }
            // line 17
            echo "    ";
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getReferralPageURL", [], "method")) {
                // line 18
                echo "      <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getReferralPageURL", [], "method"), "html", null, true);
                echo "\" target=\"_blank\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Sign Up"]), "html", null, true);
                echo "</a>
    ";
            }
            // line 20
            echo "  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/items_list/payment/methods/parts/action.right.after.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 20,  58 => 18,  55 => 17,  51 => 15,  48 => 14,  40 => 12,  38 => 11,  35 => 10,  33 => 9,  30 => 8,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/CDev/Paypal/items_list/payment/methods/parts/action.right.after.twig", "");
    }
}
