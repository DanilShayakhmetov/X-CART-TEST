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

/* main_center/page_container_parts/header_parts/storefront_status.twig */
class __TwigTemplate_0f9ed74f2b227f6793ceb40ed2720be7957aea3983343c471005e7c26b4db46c extends \XLite\Core\Templating\Twig\Template
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
        ob_start();
        // line 6
        echo "  <div ";
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getContainerTagAttributes", [], "method")], "method");
        echo ">
    <a href=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getOpenedShopURL", [], "method"), "html", null, true);
        echo "\" class=\"link opened\" target=\"_blank\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStorefrontLinkLabel", [], "method"), "html", null, true);
        echo "</a>
    <a href=\"";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getClosedShopURL", [], "method"), "html", null, true);
        echo "\" class=\"link closed\" target=\"_blank\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Private link"]), "html", null, true);
        echo "\">
      ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStorefrontLinkLabel", [], "method"), "html", null, true);
        echo "
    </a>
    ";
        // line 11
        if ($this->getAttribute(($context["this"] ?? null), "isTogglerVisible", [], "method")) {
            // line 12
            echo "      <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLink", [], "method"), "html", null, true);
            echo "\" ";
            echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getTogglerTagAttributes", [], "method")], "method");
            echo ">
        <div class=\"onoffswitch\">
          <label for=\"\">
            <div class=\"onoffswitch-inner\">
              <div class=\"on-caption\">";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["On"]), "html", null, true);
            echo "</div>
              <div class=\"off-caption\">";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Off"]), "html", null, true);
            echo "</div>
            </div>
            <span class=\"onoffswitch-switch\"></span>
          </label>
        </div>
      </a>
    ";
        }
        // line 24
        echo "    ";
        // line 25
        echo "  </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "main_center/page_container_parts/header_parts/storefront_status.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 25,  83 => 24,  73 => 17,  69 => 16,  59 => 12,  57 => 11,  52 => 9,  46 => 8,  40 => 7,  35 => 6,  33 => 5,  30 => 4,);
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
 # Storefront status
 #}

{% spaceless %}
  <div {{ this.printTagAttributes(this.getContainerTagAttributes())|raw }}>
    <a href=\"{{ this.getOpenedShopURL() }}\" class=\"link opened\" target=\"_blank\">{{ this.getStorefrontLinkLabel() }}</a>
    <a href=\"{{ this.getClosedShopURL() }}\" class=\"link closed\" target=\"_blank\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"{{ t('Private link') }}\">
      {{ this.getStorefrontLinkLabel() }}
    </a>
    {% if this.isTogglerVisible() %}
      <a href=\"{{ this.getLink() }}\" {{ this.printTagAttributes(this.getTogglerTagAttributes())|raw }}>
        <div class=\"onoffswitch\">
          <label for=\"\">
            <div class=\"onoffswitch-inner\">
              <div class=\"on-caption\">{{ t('On') }}</div>
              <div class=\"off-caption\">{{ t('Off') }}</div>
            </div>
            <span class=\"onoffswitch-switch\"></span>
          </label>
        </div>
      </a>
    {% endif %}
    {#<span class=\"link closed\">{{ this.getCloseTitle()|raw }}</span>#}
  </div>
{% endspaceless %}
", "main_center/page_container_parts/header_parts/storefront_status.twig", "/mff/xcart/skins/admin/main_center/page_container_parts/header_parts/storefront_status.twig");
    }
}
