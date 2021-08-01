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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/CustomProductTabs/product/brief_info.twig */
class __TwigTemplate_3bece62d9646b04e968205a26d99fdc5723a19e796205dbb6ffc3f54abb48da1 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "hasTabsBriefInfo", [], "method")) {
            // line 8
            echo "  <div class=\"product-tabs-brief-info\">
    <ul>
      ";
            // line 10
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getTabsBriefInfo", [], "method"));
            foreach ($context['_seq'] as $context["link"] => $context["info"]) {
                // line 11
                echo "        <li>
          <span class=\"tab-title\">";
                // line 12
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["info"], "title", []), "html", null, true);
                echo "</span>
          <div class=\"tab-brief-info-body\">
            ";
                // line 14
                echo $this->getAttribute($context["info"], "brief_info", []);
                echo "
          </div>
          <div class=\"brief-info-link\">
            <a data-id=\"";
                // line 17
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["link"], "html", null, true);
                echo "\" href=\"#";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["link"], "html", null, true);
                echo "\" data-toggle=\"tab\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["learn more"]), "html", null, true);
                echo "</a>
          </div>
        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['link'], $context['info'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "    </ul>
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/CustomProductTabs/product/brief_info.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 21,  57 => 17,  51 => 14,  46 => 12,  43 => 11,  39 => 10,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/CustomProductTabs/product/brief_info.twig", "");
    }
}
