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

/* layout/slidebar.twig */
class __TwigTemplate_b33197e8805c40c4758dab37b8b55373977d0487ad97ddad6afc5fc4b73e2ff8 extends \XLite\Core\Templating\Twig\Template
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
        echo "<nav id=\"slidebar\" data-mobile-navbar>
    ";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "shouldRender", [], "method")) {
            // line 6
            echo "    <ul>
        ";
            // line 7
            if ($this->getAttribute(($context["this"] ?? null), "isDisplayCategories", [], "method")) {
                // line 8
                echo "            <li class=\"slidebar-categories\">
                <span data-toggle=\"dropdown\">";
                // line 9
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Categories"]), "html", null, true);
                echo "</span>
              ";
                // line 10
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "layout.header.categories"]]), "html", null, true);
                echo "
            </li>
        ";
            }
            // line 13
            echo "        ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header.menu", "isSlidebar" => true]]), "html", null, true);
            echo "
    </ul>
    ";
        }
        // line 16
        echo "</nav>
";
    }

    public function getTemplateName()
    {
        return "layout/slidebar.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  60 => 16,  53 => 13,  47 => 10,  43 => 9,  40 => 8,  38 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "layout/slidebar.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/layout/slidebar.twig");
    }
}
