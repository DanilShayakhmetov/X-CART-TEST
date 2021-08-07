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

/* modules/CDev/Paypal/banner/homePage/belowProducts.twig */
class __TwigTemplate_387242e6041d7c45852110ebca501d1728d7ad83f5549d654d026b1048f7e654 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\CDev\\Paypal\\View\\Banner", "page" => "homePage", "position" => "B"]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "modules/CDev/Paypal/banner/homePage/belowProducts.twig";
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
        return new Source("{##
 # Paypal Credit banner
 #
 # @ListChild (list=\"center.bottom\", weight=\"99999\")
 #}

{{ widget('\\\\XLite\\\\Module\\\\CDev\\\\Paypal\\\\View\\\\Banner', page='homePage', position='B') }}", "modules/CDev/Paypal/banner/homePage/belowProducts.twig", "/mff/xcart/skins/customer/modules/CDev/Paypal/banner/homePage/belowProducts.twig");
    }
}
