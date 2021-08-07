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

/* modules/XC/NewsletterSubscriptions/form/subscribe.twig */
class __TwigTemplate_a7aaeba48c26a052b2d4f4321615bb884bb603f0e87fd711c4dcccce565c4f53 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"subscription-block\">
    <div class=\"subscription-form-block\">
        ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "NewsletterSubscriptions.footer.form-block"]]), "html", null, true);
        echo "

        <div class=\"subscription-error-block hidden\">
            ";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "NewsletterSubscriptions.footer.error-block"]]), "html", null, true);
        echo "
        </div>
    </div>
    <div class=\"subscription-success-block hidden\">
        ";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "NewsletterSubscriptions.footer.success-block"]]), "html", null, true);
        echo "
    </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/NewsletterSubscriptions/form/subscribe.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 14,  41 => 10,  35 => 7,  30 => 4,);
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
 # Subscribe block
 #}

<div class=\"subscription-block\">
    <div class=\"subscription-form-block\">
        {{ widget_list('NewsletterSubscriptions.footer.form-block') }}

        <div class=\"subscription-error-block hidden\">
            {{ widget_list('NewsletterSubscriptions.footer.error-block') }}
        </div>
    </div>
    <div class=\"subscription-success-block hidden\">
        {{ widget_list('NewsletterSubscriptions.footer.success-block') }}
    </div>
</div>
", "modules/XC/NewsletterSubscriptions/form/subscribe.twig", "/mff/xcart/skins/customer/modules/XC/NewsletterSubscriptions/form/subscribe.twig");
    }
}
