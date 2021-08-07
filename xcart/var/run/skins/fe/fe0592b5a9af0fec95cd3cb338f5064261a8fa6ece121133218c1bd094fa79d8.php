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

/* /mff/xcart/skins/crisp_white/customer/layout/header/header_settings/settings.twig */
class __TwigTemplate_7b8ab4bd5a41e852736fa3c7b3b0c4e9a2073af8122b345181739f5802686e7d extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "getViewList", [0 => "slidebar.settings"], "method")) {
            // line 8
            echo "  <div class=\"settings\">
    <div>
      <a href=\"#settings-panel\" class=\"item-title\">
        <span>";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Settings"]), "html", null, true);
            echo "</span>
        <span class=\"mm-next\"></span>
      </a>
      <div class=\"Panel\" id=\"settings-panel\">
        <ul class=\"Inset\">
          ";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "slidebar.settings"]]), "html", null, true);
            echo "
        </ul>
      </div>
    </div>
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/crisp_white/customer/layout/header/header_settings/settings.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 16,  40 => 11,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/crisp_white/customer/layout/header/header_settings/settings.twig", "");
    }
}
