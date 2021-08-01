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
class __TwigTemplate_2bf83a64b07dd09f934fee68898e999b285483b151a8f1f05d9256d0cba65e66 extends \XLite\Core\Templating\Twig\Template
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
<div id=\"status-messages\" class=\"top-message-container\" ";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "isHidden", [], "method")) {
            echo " style=\"display: none;\"";
        }
        echo ">

  <a href=\"#\" class=\"close-message\" title=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Close"]), "html", null, true);
        echo "\"><img src=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPath", [], "method"), "html", null, true);
        echo "/spacer3.gif\" alt=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Close"]), "html", null, true);
        echo "\" /></a>

  ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "hasTopMessages", [], "method")) {
            // line 10
            echo "    <ul class=\"top-messages\">
      ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getTopMessages", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["data"]) {
                // line 12
                echo "        <li class=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getType", [0 => $context["data"]], "method"), "html", null, true);
                echo "\">
          ";
                // line 13
                if ($this->getAttribute(($context["this"] ?? null), "getPrefix", [0 => $context["data"]], "method")) {
                    // line 14
                    echo "            <em>";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getPrefix", [0 => $context["data"]], "method"), "html", null, true);
                    echo "</em>
          ";
                }
                // line 15
                echo $this->getAttribute(($context["this"] ?? null), "getText", [0 => $context["data"]], "method");
                echo "
        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['data'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 18
            echo "    </ul>
  ";
        }
        // line 20
        echo "
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
        return array (  84 => 20,  80 => 18,  71 => 15,  65 => 14,  63 => 13,  58 => 12,  54 => 11,  51 => 10,  49 => 9,  40 => 7,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "top_message/body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/top_message/body.twig");
    }
}
