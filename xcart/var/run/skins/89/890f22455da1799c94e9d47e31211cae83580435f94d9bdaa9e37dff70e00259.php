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

/* form_field/form_field_label.twig */
class __TwigTemplate_ccb8ee9f8b7a9622cd38cdf5f3ed22d21126c6b097125945b6c1deb1790df49a extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLabelContainerClass", [], "method"), "html", null, true);
        echo "\">
  <label for=\"";
        // line 2
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFieldId", [], "method"), "html", null, true);
        echo "\">
    ";
        // line 3
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getFormattedLabel", [], "method"), "html", null, true);
        echo "
    ";
        // line 4
        if ($this->getAttribute(($context["this"] ?? null), "hasLabelHelp", [], "method")) {
            // line 5
            echo "      ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Tooltip", "text" => call_user_func_array($this->env->getFunction('t')->getCallable(), [$this->getAttribute(($context["this"] ?? null), "getParam", [0 => "labelHelp"], "method")]), "helpWidget" => $this->getAttribute(($context["this"] ?? null), "getParam", [0 => "labelHelpWidget"], "method"), "isImageTag" => "true", "className" => "help-icon"]]), "html", null, true);
            echo "
    ";
        }
        // line 7
        echo "  </label>
</div>
<div class=\"star\">
  ";
        // line 10
        if ($this->getAttribute(($context["this"] ?? null), "getParam", [0 => "required"], "method")) {
            echo "*";
        } else {
            echo "&nbsp;";
        }
        // line 11
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "form_field/form_field_label.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 11,  56 => 10,  51 => 7,  45 => 5,  43 => 4,  39 => 3,  35 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "form_field/form_field_label.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/form_field/form_field_label.twig");
    }
}
