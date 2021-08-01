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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/page/parts/placed.twig */
class __TwigTemplate_2b63370f66b35c9a6eda443acb7b3f77d3e000983d934b44bd6266556b446474 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"title\">
  <div class=\"placed-date head-h4\">";
        // line 8
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Placed on DATE", ["date" => $this->getAttribute(($context["this"] ?? null), "getOrderDate", [], "method")]]);
        echo "</div>
  <div class=\"placed-customer\">
  ";
        // line 10
        if ($this->getAttribute(($context["this"] ?? null), "hasProfilePage", [], "method")) {
            // line 11
            echo "    ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Placed by _NAME_", ["name" => $this->getAttribute(($context["this"] ?? null), "getProfileName", [], "method"), "url" => $this->getAttribute(($context["this"] ?? null), "getProfileURL", [], "method")]]);
            echo "
  ";
        } else {
            // line 13
            echo "    ";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Placed by NAME", ["name" => $this->getAttribute(($context["this"] ?? null), "getProfileName", [], "method")]]);
            echo "
  ";
        }
        // line 15
        echo "
  ";
        // line 16
        if ($this->getAttribute(($context["this"] ?? null), "isShowProfileLogin", [], "method")) {
            // line 17
            echo "    <div class=\"profile-login\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "orderForm", []), "displayComplexField", [0 => "login"], "method"), "html", null, true);
            echo "</div>
  ";
        }
        // line 19
        echo "
  ";
        // line 20
        if ($this->getAttribute(($context["this"] ?? null), "getMembership", [], "method")) {
            // line 21
            echo "    <span class=\"membership\">(";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "membership", []), "getName", [], "method"), "html", null, true);
            echo ")</span>
  ";
        }
        // line 23
        echo "  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/page/parts/placed.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 23,  69 => 21,  67 => 20,  64 => 19,  58 => 17,  56 => 16,  53 => 15,  47 => 13,  41 => 11,  39 => 10,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/page/parts/placed.twig", "");
    }
}
