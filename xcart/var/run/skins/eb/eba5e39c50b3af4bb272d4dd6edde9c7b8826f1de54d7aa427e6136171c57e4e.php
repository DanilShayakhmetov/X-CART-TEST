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

/* main.twig */
class __TwigTemplate_d66e7350d5e1f8ecb1395941bcf92b1c73207407f646f10acc210526abf130ab extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "isDeveloperMode", [], "method")) {
            // line 5
            echo "  <div id=\"profiler-messages\"></div>
";
        }
        // line 7
        echo "
";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\TopMessage"]]), "html", null, true);
        echo "

<div id=\"page-wrapper\">

  ";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("body/header.twig");        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        // line 12
        $this->loadTemplate("body/header.twig", "main.twig", 12)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 13
        echo "
  <div id=\"page-container\">
    ";
        // line 15
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "layout", []), "isSidebarFirstVisible", [], "method")) {
            // line 16
            echo "      <div id=\"sidebar-first\" class=\"side-bar\">
        ";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.content.left"]]), "html", null, true);
            echo "
      </div>
    ";
        }
        // line 20
        echo "
    <div id=\"main\">
      ";
        // line 22
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.content.center"]]), "html", null, true);
        echo "

      ";
        // line 24
        if ($this->getAttribute(($context["this"] ?? null), "isViewListVisible", [0 => "admin.main.page.content.sub_section"], "method")) {
            // line 25
            echo "        <div id=\"sub-section\">
          ";
            // line 26
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin.main.page.content.sub_section"]]), "html", null, true);
            echo "
        </div>
      ";
        }
        // line 29
        echo "
      ";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("body/footer.twig");        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        // line 30
        $this->loadTemplate("body/footer.twig", "main.twig", 30)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 31
        echo "    </div>

  </div>

</div>
";
    }

    public function getTemplateName()
    {
        return "main.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 31,  99 => 30,  91 => 29,  85 => 26,  82 => 25,  80 => 24,  75 => 22,  71 => 20,  65 => 17,  62 => 16,  60 => 15,  56 => 13,  51 => 12,  39 => 8,  36 => 7,  32 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "main.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/main.twig");
    }
}
