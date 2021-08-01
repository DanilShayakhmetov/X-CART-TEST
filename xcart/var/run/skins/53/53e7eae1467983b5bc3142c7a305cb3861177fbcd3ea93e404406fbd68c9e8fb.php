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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/addresses/addresses.merchant.twig */
class __TwigTemplate_57fe6d35619a13ac61b99f7d664df6ea0f2ff2ba4410bd59c0d918d225949d9e extends \XLite\Core\Templating\Twig\Template
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
<td class=\"merchant-data\">
  <div class=\"merchant-data-block merchant-address\">
    <h3>";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_name", []), "html", null, true);
        echo "</h3>
    <p>
      ";
        // line 11
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "location_address", [])) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "location_address", []), "html", null, true);
            echo "<br />";
        }
        // line 12
        echo "      ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "location_city", [])) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "location_city", []), "html", null, true);
            echo ", ";
        }
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "locationState", []), "state", [])) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "locationState", []), "state", []), "html", null, true);
            echo ", ";
        }
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "location_zipcode", [])) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "location_zipcode", []), "html", null, true);
        }
        echo "<br />
      ";
        // line 13
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "locationCountry", [])) {
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "locationCountry", []), "getCountry", [], "method"), "html", null, true);
        }
        // line 14
        echo "    </p>
    ";
        // line 15
        if (($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_phone", []) || $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_fax", []))) {
            // line 16
            echo "      <p>
        ";
            // line 17
            if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_phone", [])) {
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Phone"]), "html", null, true);
                echo ": ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_phone", []), "html", null, true);
                echo "<br />";
            }
            // line 18
            echo "        ";
            if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_fax", [])) {
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Fax"]), "html", null, true);
                echo ": ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_fax", []), "html", null, true);
            }
            // line 19
            echo "      </p>
    ";
        }
        // line 21
        echo "    ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_website", [])) {
            // line 22
            echo "      <p class=\"url\">
        <a href=\"";
            // line 23
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_website", []), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "config", []), "Company", []), "company_website", []), "html", null, true);
            echo "</a>
      </p>
    ";
        }
        // line 26
        echo "  </div>
</td>

";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/addresses/addresses.merchant.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 26,  96 => 23,  93 => 22,  90 => 21,  86 => 19,  79 => 18,  72 => 17,  69 => 16,  67 => 15,  64 => 14,  60 => 13,  45 => 12,  40 => 11,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/packing_slip/parts/addresses/addresses.merchant.twig", "");
    }
}
