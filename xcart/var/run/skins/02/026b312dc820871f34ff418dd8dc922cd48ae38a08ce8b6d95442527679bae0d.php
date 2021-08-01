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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/main.methods.twig */
class __TwigTemplate_a8c2278e44ca341aac7b94ee2687e6b3d9633ca42fcebf395b65a9572c8b8902 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isEstimate", [], "method")) {
            // line 7
            echo "  <div class=\"estimate-methods\">
    <div class=\"head-h3\">";
            // line 8
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Choose shipping method"]), "html", null, true);
            echo "</div>
    ";
            // line 9
            $this->startForm("XLite\\View\\Form\\Cart\\ShippingEstimator\\Change", ["className" => "method-change"]);            // line 10
            echo "  
      ";
            // line 11
            if ($this->getAttribute(($context["this"] ?? null), "hasRates", [], "method")) {
                // line 12
                echo "        ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\ShippingList"]]), "html", null, true);
                echo "
  
        <div class=\"buttons main\">
          ";
                // line 15
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Submit", "label" => "Save"]]), "html", null, true);
                echo "
        </div>
      ";
            } else {
                // line 18
                echo "        <p class=\"error\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Shipping methods are not available"]), "html", null, true);
                echo "</p>
      ";
            }
            // line 20
            echo "  
    ";
            $this->endForm();            // line 22
            echo "  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/main.methods.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  67 => 22,  64 => 20,  58 => 18,  52 => 15,  45 => 12,  43 => 11,  40 => 10,  39 => 9,  35 => 8,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/shopping_cart/shipping_estimator/parts/main.methods.twig", "");
    }
}
