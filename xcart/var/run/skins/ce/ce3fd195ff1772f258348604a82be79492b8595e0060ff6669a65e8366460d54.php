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

/* modules/XC/ProductComparison/comparison_table/parts/attributes/body.twig */
class __TwigTemplate_cb9c29cec296572be5795448c9718d7f87c6a5db6064461709da7b8063ad448b extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "getProductClasses", [], "method")) {
            // line 6
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ProductComparison\\View\\ComparisonTable\\AttributeList", "classes" => $this->getAttribute(($context["this"] ?? null), "getProductClasses", [], "method")]]), "html", null, true);
            echo "
";
        }
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ProductComparison\\View\\ComparisonTable\\AttributeList"]]), "html", null, true);
        echo "
";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getProductClasses", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["productClass"]) {
            // line 10
            echo "  ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["productClass"], "getAttributeGroups", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["group"]) {
                // line 11
                echo "    ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ProductComparison\\View\\ComparisonTable\\AttributeList", "group" => $context["group"]]]), "html", null, true);
                echo "
  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['group'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['productClass'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getGlobalGroups", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["group"]) {
            // line 15
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\ProductComparison\\View\\ComparisonTable\\AttributeList", "group" => $context["group"]]]), "html", null, true);
            echo "
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['group'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "modules/XC/ProductComparison/comparison_table/parts/attributes/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 15,  67 => 14,  54 => 11,  49 => 10,  45 => 9,  41 => 8,  35 => 6,  33 => 5,  30 => 4,);
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
 # Attributes 
 #}

{% if this.getProductClasses() %}
  {{ widget('XLite\\\\Module\\\\XC\\\\ProductComparison\\\\View\\\\ComparisonTable\\\\AttributeList', classes=this.getProductClasses()) }}
{% endif %}
{{ widget('XLite\\\\Module\\\\XC\\\\ProductComparison\\\\View\\\\ComparisonTable\\\\AttributeList') }}
{% for productClass in this.getProductClasses() %}
  {% for group in productClass.getAttributeGroups() %}
    {{ widget('XLite\\\\Module\\\\XC\\\\ProductComparison\\\\View\\\\ComparisonTable\\\\AttributeList', group=group) }}
  {% endfor %}
{% endfor %}
{% for group in this.getGlobalGroups() %}
  {{ widget('XLite\\\\Module\\\\XC\\\\ProductComparison\\\\View\\\\ComparisonTable\\\\AttributeList', group=group) }}
{% endfor %}
", "modules/XC/ProductComparison/comparison_table/parts/attributes/body.twig", "/mff/xcart/skins/customer/modules/XC/ProductComparison/comparison_table/parts/attributes/body.twig");
    }
}
