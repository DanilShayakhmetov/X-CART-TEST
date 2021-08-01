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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/main.address.twig */
class __TwigTemplate_b188bc37d2df03213837fcd46d6482d124ecf6d8124d18d92b11974319efd9dd extends \XLite\Core\Templating\Twig\Template
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
        $this->startForm("\\XLite\\View\\Form\\Cart\\ShippingEstimator\\Destination", ["className" => "estimator"]);        // line 7
        echo "
  <ul class=\"form\">
    ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "shippingEstimator.address"]]), "html", null, true);
        echo "
  </ul>

  ";
        // line 12
        if ($this->getAttribute(($context["this"] ?? null), "isEstimate", [], "method")) {
            // line 13
            echo "    <div class=\"buttons\">
      ";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Submit", "label" => "Apply destination"]]), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 17
        echo "
  ";
        // line 18
        if ( !$this->getAttribute(($context["this"] ?? null), "isEstimate", [], "method")) {
            // line 19
            echo "    <div class=\"buttons main\">
      ";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Button\\Submit", "label" => "Apply destination"]]), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 23
        echo "
";
        $this->endForm();    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/main.address.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 23,  60 => 20,  57 => 19,  55 => 18,  52 => 17,  46 => 14,  43 => 13,  41 => 12,  35 => 9,  31 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/main.address.twig", "");
    }
}
