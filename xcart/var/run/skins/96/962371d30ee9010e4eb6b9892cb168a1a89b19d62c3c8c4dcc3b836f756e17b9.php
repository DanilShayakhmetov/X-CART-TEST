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

/* shopping_cart/parts/box.estimator.twig */
class __TwigTemplate_9e05114214972b91ad5f2daa596afccbe8e0c6408d61b79e9645c722a2e70825 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"estimator\" data-deferred=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "shouldDeferLoad", [], "method"), "html", null, true);
        echo "\" data-shipping-cost=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getShippingCost", [], "method"), "html", null, true);
        echo "\">

  ";
        // line 6
        if ($this->getAttribute(($context["this"] ?? null), "isShippingEstimate", [], "method")) {
            // line 7
            echo "
    <ul>
      <li>
        <span class=\"section\">";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Shipping"]), "html", null, true);
            echo ":</span>
        ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\ShippingEstimator\\SelectedMethod"]]), "html", null, true);
            echo "
      </li>
      <li>
        <span class=\"section\">";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Estimated for"]), "html", null, true);
            echo ":</span>
        ";
            // line 15
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEstimateAddress", [], "method"), "html", null, true);
            echo "
      </li>
    </ul>

    <div class=\"link\">
      <a href=\"";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "shipping_estimate"]), "html", null, true);
            echo "\" class=\"estimate\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Change method"]), "html", null, true);
            echo "</a>
    </div>

  ";
        } else {
            // line 24
            echo "
    ";
            // line 25
            $this->startForm("XLite\\View\\Form\\Cart\\ShippingEstimator\\Open");            // line 26
            echo "      <div class=\"buttons\">
        ";
            // line 27
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Submit", "label" => "Estimate shipping cost", "style" => "estimate"]]), "html", null, true);
            echo "
      </div>
    ";
            $this->endForm();            // line 30
            echo "
  ";
        }
        // line 32
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "shopping_cart/parts/box.estimator.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 32,  88 => 30,  83 => 27,  80 => 26,  79 => 25,  76 => 24,  67 => 20,  59 => 15,  55 => 14,  49 => 11,  45 => 10,  40 => 7,  38 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "shopping_cart/parts/box.estimator.twig", "/mff/xcart/skins/customer/shopping_cart/parts/box.estimator.twig");
    }
}
