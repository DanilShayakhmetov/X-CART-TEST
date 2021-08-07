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

/* modules/creator/wishlist/header_settings_link.twig */
class __TwigTemplate_1f2d7dba0ed9ca4101a67cd6b80392c9b29a37289d0a9a804822c86ce35ba4b5 extends \XLite\Core\Templating\Twig\Template
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
<li class=\"account-link-compare compare-indicator ";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getIndicatorClasses", [], "method"), "html", null, true);
        echo "\">
  <a ";
        // line 6
        if ( !$this->getAttribute(($context["this"] ?? null), "isDisabled", [], "method")) {
            echo "href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCompareURL", [], "method"), "html", null, true);
            echo "\"";
        }
        echo " data-target=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getCompareURL", [], "method"), "html", null, true);
        echo "\" title=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLinkHelpMessage", [], "method"), "html", null, true);
        echo "\">
\t<span>";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["wishlist"]), "html", null, true);
        echo "</span>
\t<span class=\"counter\">";
        // line 8
        ((($this->getAttribute(($context["this"] ?? null), "getComparedCount", [], "method") > 0)) ? (print (XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getComparedCount", [], "method"), "html", null, true))) : (print ("")));
        echo "</span>
  </a>
</li>
";
    }

    public function getTemplateName()
    {
        return "modules/creator/wishlist/header_settings_link.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 8,  49 => 7,  37 => 6,  33 => 5,  30 => 4,);
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
 # Compare link
 #}

<li class=\"account-link-compare compare-indicator {{ this.getIndicatorClasses() }}\">
  <a {% if not this.isDisabled() %}href=\"{{ this.getCompareURL() }}\"{% endif %} data-target=\"{{ this.getCompareURL() }}\" title=\"{{ this.getLinkHelpMessage() }}\">
\t<span>{{ t('wishlist') }}</span>
\t<span class=\"counter\">{{ this.getComparedCount() > 0 ? this.getComparedCount() : \"\" }}</span>
  </a>
</li>
", "modules/creator/wishlist/header_settings_link.twig", "/mff/xcart/skins/crisp_white/customer/modules/creator/wishlist/header_settings_link.twig");
    }
}
