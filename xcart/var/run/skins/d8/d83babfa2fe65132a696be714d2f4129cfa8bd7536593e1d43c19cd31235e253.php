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

/* layout/content/main.location.twig */
class __TwigTemplate_30f328b0d253056d1866f9cf57eb3ee9859b677332ceaf32d67a89f5b67a34f4 extends \XLite\Core\Templating\Twig\Template
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
        if ((($this->getAttribute(($context["this"] ?? null), "isTitleVisible", [], "method") && $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method")) || $this->getAttribute(($context["this"] ?? null), "isShowAdditionalMobileBreadcrumbs", [], "method"))) {
            // line 5
            echo "  <div id=\"";
            echo (($this->getAttribute(($context["this"] ?? null), "isShowAdditionalMobileBreadcrumbs", [], "method")) ? ("mobile-breadcrumb") : ("breadcrumb"));
            echo "\">
      ";
            // line 6
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "layout.main.breadcrumb"]]), "html", null, true);
            echo "
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "layout/content/main.location.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 6,  32 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{##
 # Location (breadcrumbs)
 #}
{% if (this.isTitleVisible() and this.getTitle()) or this.isShowAdditionalMobileBreadcrumbs() %}
  <div id=\"{{ this.isShowAdditionalMobileBreadcrumbs() ? 'mobile-breadcrumb' : 'breadcrumb' }}\">
      {{ widget_list('layout.main.breadcrumb') }}
  </div>
{% endif %}
", "layout/content/main.location.twig", "/mff/xcart/skins/crisp_white/customer/layout/content/main.location.twig");
    }
}
