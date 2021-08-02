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

/* form_field/select.twig */
class __TwigTemplate_a5398537e792b6d5680faa086e1e23ef64b0093b3a1a0dbce4d5ace12df7d66a extends \XLite\Core\Templating\Twig\Template
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
<span class=\"input-field-wrapper ";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getWrapperClass", [], "method"), "html", null, true);
        echo "\">
  <select ";
        // line 6
        echo $this->getAttribute(($context["this"] ?? null), "getAttributesCode", [], "method");
        echo ">
    ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getCommentedData", [], "method")], "method"), "html", null, true);
        echo "
    ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getOptions", [], "method"));
        foreach ($context['_seq'] as $context["optionValue"] => $context["optionLabel"]) {
            // line 9
            echo "      ";
            if ($this->getAttribute(($context["this"] ?? null), "isGroup", [0 => $context["optionLabel"]], "method")) {
                // line 10
                echo "        <optgroup ";
                echo $this->getAttribute(($context["this"] ?? null), "getOptionGroupAttributesCode", [0 => $context["optionValue"], 1 => $context["optionLabel"]], "method");
                echo ">
          ";
                // line 11
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["optionLabel"], "options", []));
                foreach ($context['_seq'] as $context["optionValue2"] => $context["optionLabel2"]) {
                    // line 12
                    echo "            <option ";
                    echo $this->getAttribute(($context["this"] ?? null), "getOptionAttributesCode", [0 => $context["optionValue2"], 1 => $context["optionLabel2"]], "method");
                    echo ">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["optionLabel2"], "html", null, true);
                    echo "</option>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['optionValue2'], $context['optionLabel2'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 14
                echo "        </optgroup>
      ";
            } else {
                // line 16
                echo "        <option ";
                echo $this->getAttribute(($context["this"] ?? null), "getOptionAttributesCode", [0 => $context["optionValue"], 1 => $context["optionLabel"]], "method");
                echo ">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["optionLabel"], "html", null, true);
                echo "</option>
      ";
            }
            // line 18
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['optionValue'], $context['optionLabel'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 19
        echo "  </select>
</span>";
    }

    public function getTemplateName()
    {
        return "form_field/select.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 19,  84 => 18,  76 => 16,  72 => 14,  61 => 12,  57 => 11,  52 => 10,  49 => 9,  45 => 8,  41 => 7,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "form_field/select.twig", "/mff/xcart/skins/admin/form_field/select.twig");
    }
}
