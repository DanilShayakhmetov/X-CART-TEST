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

/* layout/content/center.twig */
class __TwigTemplate_3e0efa8a5e349fc5edbb3be363f65ccac22ba6158d30741afc75674c762b9808 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "center.top"]]), "html", null, true);
        echo "

";
        // line 7
        if (($this->getAttribute(($context["this"] ?? null), "isTitleVisible", [], "method") && $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method"))) {
            // line 8
            echo "  <h1 class=\"title\" id=\"page-title\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getMainTitle", [], "method"), "html", null, true);
            echo "</h1>
";
        }
        // line 10
        echo "
";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("layout/content/center_top.twig");        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        // line 11
        $this->loadTemplate("layout/content/center_top.twig", "layout/content/center.twig", 11)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 12
        echo "
";
        // line 13
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "center.bottom"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "layout/content/center.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 13,  59 => 12,  54 => 11,  46 => 10,  40 => 8,  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "layout/content/center.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/layout/content/center.twig");
    }
}
