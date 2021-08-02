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

/* location/node.twig */
class __TwigTemplate_20c59ed33c84868db9ce8e609dbde839a5c32f83edea944fbd59394fc2e6361f extends \XLite\Core\Templating\Twig\Template
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
<li ";
        // line 5
        echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getListContainerAttributes", [], "method")], "method");
        echo ">

  ";
        // line 7
        if ($this->getAttribute(($context["this"] ?? null), "getLink", [], "method")) {
            // line 8
            echo "    <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLink", [], "method"), "html", null, true);
            echo "\" class=\"location-title\"><span>";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "</span></a>
  ";
        } else {
            // line 10
            echo "    <span class=\"location-text\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "</span>
  ";
        }
        // line 12
        echo "
  ";
        // line 13
        if ($this->getAttribute(($context["this"] ?? null), "getSubnodes", [], "method")) {
            // line 14
            echo "    <ul class=\"location-subnodes\">
      ";
            // line 15
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getSubnodes", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["node"]) {
                // line 16
                echo "        <li>
          ";
                // line 17
                if (($this->getAttribute($context["node"], "getName", [], "method") != $this->getAttribute(($context["this"] ?? null), "getName", [], "method"))) {
                    // line 18
                    echo "            <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["node"], "getLink", [], "method"), "html", null, true);
                    echo "\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["node"], "getName", [], "method"), "html", null, true);
                    echo "</a>
          ";
                }
                // line 20
                echo "          ";
                if (($this->getAttribute($context["node"], "getName", [], "method") == $this->getAttribute(($context["this"] ?? null), "getName", [], "method"))) {
                    // line 21
                    echo "            <a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["node"], "getLink", [], "method"), "html", null, true);
                    echo "\" class=\"current\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["node"], "getName", [], "method"), "html", null, true);
                    echo "</a>
          ";
                }
                // line 23
                echo "        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['node'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 25
            echo "      ";
            if (($this->getAttribute(($context["this"] ?? null), "moreLinkNeeded", [], "method") && $this->getAttribute(($context["this"] ?? null), "getLink", [], "method"))) {
                // line 26
                echo "        <li class='more-link'>
            <a href=\"";
                // line 27
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getMoreLink", [], "method"), "html", null, true);
                echo "\" class=\"location-title\"><span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["More"]), "html", null, true);
                echo "...</span></a>
        </li>
      ";
            }
            // line 30
            echo "    </ul>
  ";
        }
        // line 32
        echo "
</li>
";
    }

    public function getTemplateName()
    {
        return "location/node.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  115 => 32,  111 => 30,  103 => 27,  100 => 26,  97 => 25,  90 => 23,  82 => 21,  79 => 20,  71 => 18,  69 => 17,  66 => 16,  62 => 15,  59 => 14,  57 => 13,  54 => 12,  48 => 10,  40 => 8,  38 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "location/node.twig", "/mff/xcart/skins/admin/location/node.twig");
    }
}
