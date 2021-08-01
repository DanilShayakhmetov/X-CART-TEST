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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XC/Reviews/review/promo_settings.twig */
class __TwigTemplate_69f98bfab561a2fc4b2cef215aa7b4247b07da6f308d677576f74644cfc85be7 extends \XLite\Core\Templating\Twig\Template
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
        // line 1
        echo "
";
        // line 7
        echo "
";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "getModule", [], "method")) {
            // line 9
            echo "
  ";
            // line 10
            $context["moduleName"] = $this->getAttribute(($context["this"] ?? null), "getModule", [], "method");
            // line 11
            echo "
  ";
            // line 12
            if ((($context["moduleName"] ?? null) == "XC\\Reviews")) {
                // line 13
                echo "    ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\Reviews\\View\\Promo\\ShopperApproved"]]), "html", null, true);
                echo "
  ";
            }
            // line 15
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XC/Reviews/review/promo_settings.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 15,  48 => 13,  46 => 12,  43 => 11,  41 => 10,  38 => 9,  36 => 8,  33 => 7,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/modules/XC/Reviews/review/promo_settings.twig", "");
    }
}
