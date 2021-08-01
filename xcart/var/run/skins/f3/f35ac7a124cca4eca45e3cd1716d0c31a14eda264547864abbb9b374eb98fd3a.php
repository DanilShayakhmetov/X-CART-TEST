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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/404/parts/contact_info.twig */
class __TwigTemplate_d54077881fcc20c5b516b374d025f3437cb4a24e8659e53029dd987cb2be5717 extends \XLite\Core\Templating\Twig\Template
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
        // line 8
        echo "
<div class=\"page-404\">
  ";
        // line 10
        if ($this->getAttribute(($context["this"] ?? null), "getPhone", [], "method")) {
            // line 11
            echo "    <div class=\"phone\">
      <span class=\"name\">";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Phone"]), "html", null, true);
            echo ":</span>
      <span class=\"value\">";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPhone", [], "method"), "html", null, true);
            echo "</span>
    </div>
  ";
        }
        // line 16
        echo "  ";
        if (($this->getAttribute(($context["this"] ?? null), "isShowEmail", [], "method") && $this->getAttribute(($context["this"] ?? null), "getEmail", [], "method"))) {
            // line 17
            echo "    <div class=\"email\">
      <a href=\"mailto:";
            // line 18
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEmail", [], "method"), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEmail", [], "method"), "html", null, true);
            echo "</a>
    </div>
  ";
        }
        // line 21
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/404/parts/contact_info.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  63 => 21,  55 => 18,  52 => 17,  49 => 16,  43 => 13,  39 => 12,  36 => 11,  34 => 10,  30 => 8,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/404/parts/contact_info.twig", "");
    }
}
