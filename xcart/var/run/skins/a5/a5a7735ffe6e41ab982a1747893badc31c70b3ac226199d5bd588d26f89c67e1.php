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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/address/text/parts/field.twig */
class __TwigTemplate_69a8fe6dd168a16549810bc1d214c401f46fc0e3cd3940724baf449f9159c149 extends \XLite\Core\Templating\Twig\Template
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
<ul class=\"address-text\">

  ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "before", "type" => "nested", "fieldName" => $this->getAttribute(($context["this"] ?? null), "fieldName", []), "fieldData" => $this->getAttribute(($context["this"] ?? null), "fieldData", [])]]), "html", null, true);
        echo "

  <li class=\"address-text-label-";
        // line 11
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "fieldName", []), "html", null, true);
        echo "\">
    ";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "fieldData", []), "label", []), "html", null, true);
        echo ":
  </li>

  ";
        // line 15
        $context["fieldValue"] = $this->getAttribute(($context["this"] ?? null), "getFieldValue", [0 => $this->getAttribute(($context["this"] ?? null), "fieldName", []), 1 => 1], "method");
        // line 16
        echo "
  <li class=\"address-text-value\">
    ";
        // line 18
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["fieldValue"] ?? null), "html", null, true);
        echo "
  </li>

  ";
        // line 21
        if (($context["fieldValue"] ?? null)) {
            // line 22
            echo "    <li class=\"address-text-comma-";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "fieldName", []), "html", null, true);
            echo "\">,</li>
  ";
        }
        // line 24
        echo "
  ";
        // line 25
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "after", "type" => "nested", "fieldName" => $this->getAttribute(($context["this"] ?? null), "fieldName", []), "fieldData" => $this->getAttribute(($context["this"] ?? null), "fieldData", [])]]), "html", null, true);
        echo "

</ul>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/address/text/parts/field.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 25,  70 => 24,  64 => 22,  62 => 21,  56 => 18,  52 => 16,  50 => 15,  44 => 12,  40 => 11,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/address/text/parts/field.twig", "");
    }
}
