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

/* dashboard/info_block/alert/body.twig */
class __TwigTemplate_51e7bbb3a5930d1303e3ed96c5981b81eb418cf0ef14f57d296e3b06268cf22f extends \XLite\Core\Templating\Twig\Template
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
<div ";
        // line 5
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getTagAttributes", [], "method")], "method");
        echo ">

  ";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "getHeaderURL", [], "method")) {
            // line 8
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getHeaderURL", [], "method"), "html", null, true);
            echo "\"";
            if ($this->getAttribute(($context["this"] ?? null), "isExternal", [], "method")) {
                echo " target=\"_blank\"";
            }
            echo ">
      <div class=\"alert-header\">
        ";
            // line 10
            echo $this->getAttribute(($context["this"] ?? null), "getIcon", [], "method");
            echo "
        <span class=\"alert-message\">";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getHeader", [], "method"), "html", null, true);
            echo "</span>
        ";
            // line 12
            if ($this->getAttribute(($context["this"] ?? null), "getCounter", [], "method")) {
                // line 13
                echo "          <div class=\"alert-counter\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCounter", [], "method"), "html", null, true);
                echo "</div>
        ";
            }
            // line 15
            echo "      </div>
    </a>
  ";
        } else {
            // line 18
            echo "    <div class=\"alert-header\">
      ";
            // line 19
            echo $this->getAttribute(($context["this"] ?? null), "getIcon", [], "method");
            echo "
      <span class=\"alert-message\">";
            // line 20
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getHeader", [], "method"), "html", null, true);
            echo "</span>
      ";
            // line 21
            if ($this->getAttribute(($context["this"] ?? null), "getCounter", [], "method")) {
                // line 22
                echo "        <div class=\"alert-counter\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCounter", [], "method"), "html", null, true);
                echo "</div>
      ";
            }
            // line 24
            echo "    </div>
  ";
        }
        // line 26
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "dashboard/info_block/alert/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 26,  90 => 24,  84 => 22,  82 => 21,  78 => 20,  74 => 19,  71 => 18,  66 => 15,  60 => 13,  58 => 12,  54 => 11,  50 => 10,  40 => 8,  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "dashboard/info_block/alert/body.twig", "/mff/xcart/skins/admin/dashboard/info_block/alert/body.twig");
    }
}
