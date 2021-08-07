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

/* /mff/xcart/skins/admin/promotions/simple_blocks/modules_settings.twig */
class __TwigTemplate_59593964f15c22d892fe4a5100082fc1fd778891a605e9d8cdc5a06a7e0e6e61 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "getModule", [], "method")) {
            // line 8
            echo "
  ";
            // line 9
            $context["moduleName"] = $this->getAttribute(($context["this"] ?? null), "getModule", [], "method");
            // line 10
            echo "
  ";
            // line 11
            if ((($context["moduleName"] ?? null) == "CDev\\GoogleAnalytics")) {
                // line 12
                echo "    ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\SimplePromoBlock", "promoId" => "seo-promo-1"]]), "html", null, true);
                echo "
  ";
            }
            // line 14
            echo "
  ";
            // line 15
            if ((($context["moduleName"] ?? null) == "CDev\\Egoods")) {
                // line 16
                echo "    ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\SimplePromoBlock", "promoId" => "g2a-egoods-1"]]), "html", null, true);
                echo "
  ";
            }
            // line 18
            echo "
  ";
            // line 19
            if ((($context["moduleName"] ?? null) == "CDev\\ContactUs")) {
                // line 20
                echo "    ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\SimplePromoBlock", "promoId" => "advanced-contact-us-1"]]), "html", null, true);
                echo "
  ";
            }
            // line 22
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/promotions/simple_blocks/modules_settings.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 22,  67 => 20,  65 => 19,  62 => 18,  56 => 16,  54 => 15,  51 => 14,  45 => 12,  43 => 11,  40 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/promotions/simple_blocks/modules_settings.twig", "");
    }
}
