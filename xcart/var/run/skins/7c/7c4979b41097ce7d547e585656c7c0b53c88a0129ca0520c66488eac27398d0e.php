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

/* top_message/body.twig */
class __TwigTemplate_34e5c765c0ea1ca4f6cf0a6d6a952c5846aa32dedc3da931e774b527c7e6e01a extends \XLite\Core\Templating\Twig\Template
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
<div class=\"status-messages-wrapper\">
  <div class=\"status-messages-wrapper2\">

    <div id=\"status-messages\" ";
        // line 8
        if ($this->getAttribute(($context["this"] ?? null), "isHidden", [], "method")) {
            echo " style=\"display: none;\"";
        }
        echo ">

      <a href=\"#\" class=\"close\" title=\"";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Close"]), "html", null, true);
        echo "\"><img src=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('asset')->getCallable(), ["images/spacer.gif"]), "html", null, true);
        echo "\" alt=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Close"]), "html", null, true);
        echo "\" /></a>

      ";
        // line 12
        if ($this->getAttribute(($context["this"] ?? null), "hasTopMessages", [], "method")) {
            // line 13
            echo "        <ul>
          ";
            // line 14
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getTopMessages", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["data"]) {
                // line 15
                echo "            <li class=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getType", [0 => $context["data"]], "method"), "html", null, true);
                echo "\">
              ";
                // line 16
                if ($this->getAttribute(($context["this"] ?? null), "getPrefix", [0 => $context["data"]], "method")) {
                    // line 17
                    echo "                <em>";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPrefix", [0 => $context["data"]], "method"), "html", null, true);
                    echo "</em>
              ";
                }
                // line 18
                echo $this->getAttribute(($context["this"] ?? null), "getText", [0 => $context["data"]], "method");
                echo "
            </li>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "        </ul>
      ";
        }
        // line 23
        echo "
    </div>

  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "top_message/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 23,  83 => 21,  74 => 18,  68 => 17,  66 => 16,  61 => 15,  57 => 14,  54 => 13,  52 => 12,  43 => 10,  36 => 8,  30 => 4,);
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
 # Top messages
 #}

<div class=\"status-messages-wrapper\">
  <div class=\"status-messages-wrapper2\">

    <div id=\"status-messages\" {% if this.isHidden() %} style=\"display: none;\"{% endif %}>

      <a href=\"#\" class=\"close\" title=\"{{ t('Close') }}\"><img src=\"{{ asset('images/spacer.gif') }}\" alt=\"{{ t('Close') }}\" /></a>

      {% if this.hasTopMessages() %}
        <ul>
          {% for data in this.getTopMessages() %}
            <li class=\"{{ this.getType(data) }}\">
              {% if this.getPrefix(data) %}
                <em>{{ this.getPrefix(data) }}</em>
              {% endif %}{{ this.getText(data)|raw }}
            </li>
          {% endfor %}
        </ul>
      {% endif %}

    </div>

  </div>
</div>
", "top_message/body.twig", "/mff/xcart/skins/customer/top_message/body.twig");
    }
}
