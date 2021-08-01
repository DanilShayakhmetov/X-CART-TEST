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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/product/attributes/parts/action.remove.twig */
class __TwigTemplate_d142cb1ba48224559121c341492efd007a556d3a6f787fe9bd0e81cd7ebfe3ae extends \XLite\Core\Templating\Twig\Template
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
";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "isRemovable", [], "method")) {
            // line 8
            echo "  <div class=\"actions\">
    ";
            // line 9
            if ($this->getAttribute(($context["this"] ?? null), "getPersonalOnly", [], "method")) {
                // line 10
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Remove", "buttonName" => (("delete[" . $this->getAttribute(($context["this"] ?? null), "itemId", [])) . "]"), "label" => $this->getAttribute(($context["this"] ?? null), "getPemoveText", [], "method"), "style" => "delete"]]), "html", null, true);
                echo "
    ";
            } else {
                // line 12
                echo "      ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\MakeAttributeBlank", "buttonName" => (("makeblank[" . $this->getAttribute(($context["this"] ?? null), "itemId", [])) . "]"), "popoverText" => $this->getAttribute(($context["this"] ?? null), "getPopoverText", [], "method"), "style" => "make-blank"]]), "html", null, true);
                echo "
    ";
            }
            // line 14
            echo "  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/product/attributes/parts/action.remove.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 14,  46 => 12,  40 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/product/attributes/parts/action.remove.twig", "");
    }
}
