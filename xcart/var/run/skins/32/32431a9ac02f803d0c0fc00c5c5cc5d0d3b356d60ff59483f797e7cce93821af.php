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

/* items_list/product/center/grid/product.twig */
class __TwigTemplate_4c21f4ef44f03d2ece2e868e086257bda3eda4f1748f6689e03d2fedc8467e2c extends \XLite\Core\Templating\Twig\Template
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
";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "head", "type" => "inherited", "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
        echo "

<div class=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getProductCellClass", [], "method"), "html", null, true);
        echo "\">
  ";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "mainBlock", "type" => "inherited", "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
        echo "
  <div class=\"infoBlock\">
      ";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "info", "type" => "inherited", "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
        echo "
  </div>
</div>

";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "tail", "type" => "inherited", "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "items_list/product/center/grid/product.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 14,  47 => 10,  42 => 8,  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/product/center/grid/product.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/items_list/product/center/grid/product.twig");
    }
}
