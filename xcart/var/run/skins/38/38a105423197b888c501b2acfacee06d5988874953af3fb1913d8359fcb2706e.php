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

/* /mff/xcart/skins/admin/items_list/payment/methods/parts/offline.actions.right.twig */
class __TwigTemplate_c117376710460eb4763be3fe7e5c6e47f1c8953964c29919f289a320c8b19426 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "hasRightActions", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
            // line 7
            echo "  <div class=\"right-actions\">
    <div class=\"switcher action\">
      ";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Checkbox\\PaymentMethod", "fieldOnly" => true, "value" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "isEnabled", [], "method"), "fieldName" => ("payment_id_" . $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")), "methodId" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")]]), "html", null, true);
            echo "
    </div>

    <div class=\"separator\"></div>
    <div class=\"configure action\">
      ";
            // line 14
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Link", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Configure"]), "location" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getConfigurationURL", [], "method"), "style" => "configure"]]), "html", null, true);
            echo "
    </div>

    <div class=\"separator\"></div>
    <div class=\"remove action\">
      ";
            // line 19
            if ($this->getAttribute(($context["this"] ?? null), "canRemoveMethod", [0 => $this->getAttribute(($context["this"] ?? null), "method", [])], "method")) {
                // line 20
                echo "        <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "payment_settings", "remove", ["id" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getMethodId", [], "method")]]), "html", null, true);
                echo "\" class=\"fa fa-trash-o\" title=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Remove"]), "html", null, true);
                echo "\"></a>
      ";
            }
            // line 22
            echo "    </div>
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/items_list/payment/methods/parts/offline.actions.right.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 22,  54 => 20,  52 => 19,  44 => 14,  36 => 9,  32 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/items_list/payment/methods/parts/offline.actions.right.twig", "");
    }
}
