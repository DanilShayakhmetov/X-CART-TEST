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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/payment/methods/parts/header.switcher.twig */
class __TwigTemplate_e0d355c027400cd169427d37b40e5aae0e147456dbaa1bfe9b88e4a861f64d46 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"switcher\">
  ";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "canSwitch", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
            // line 8
            echo "    ";
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getWarningNote", [], "method")) {
                // line 9
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Checkbox\\PaymentMethod", "fieldOnly" => true, "value" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "isEnabled", [], "method"), "fieldName" => ("payment_id_" . $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")), "methodId" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method"), "disabled" =>  !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "isEnabled", [], "method"), "disabled_title" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getWarningNote", [], "method")]]), "html", null, true);
                echo "

    ";
            } else {
                // line 12
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Checkbox\\PaymentMethod", "fieldOnly" => true, "value" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "isEnabled", [], "method"), "fieldName" => ("payment_id_" . $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")), "methodId" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")]]), "html", null, true);
                echo "

      ";
                // line 15
                echo "        ";
                // line 16
                echo "      ";
                // line 17
                echo "        ";
                // line 18
                echo "      ";
                // line 19
                echo "
    ";
            }
            // line 21
            echo "  ";
        } else {
            // line 22
            echo "    ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Checkbox\\PaymentMethod", "fieldOnly" => true, "value" => $this->getAttribute(($context["this"] ?? null), "canEnable", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method"), "fieldName" => ("payment_id_" . $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")), "methodId" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method"), "disabled" => true, "disabled_title" => (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "isEnabled", [], "method")) ? ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getForcedEnabledNote", [], "method")) : ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getWarningNote", [], "method")))]]), "html", null, true);
            echo "

  ";
        }
        // line 25
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/payment/methods/parts/header.switcher.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 25,  66 => 22,  63 => 21,  59 => 19,  57 => 18,  55 => 17,  53 => 16,  51 => 15,  45 => 12,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/items_list/payment/methods/parts/header.switcher.twig", "");
    }
}
