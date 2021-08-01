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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/import/parts/failed.errors.twig */
class __TwigTemplate_4caf6696d988b1e63f2bc0bbd0515bff2debb60ce96e9cd78a42acf8ef5d43ad extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "import.failed.content.errors"]]), "html", null, true);
        echo "

";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "hasErrors", [], "method")) {
            // line 10
            echo "  <div class=\"alert alert-danger\">
  ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Critical errors have been detected in the files you are trying to import. Check the manual to correct the errors and try again."]), "html", null, true);
            echo "
  ";
            // line 12
            $context["manualLinks"] = $this->getAttribute(($context["this"] ?? null), "getManualLinks", [], "method");
            // line 13
            echo "  ";
            if ( !twig_test_empty(($context["manualLinks"] ?? null))) {
                // line 14
                echo "    <div>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["CSV format tables"]), "html", null, true);
                echo ":</div>
    <ul>
      ";
                // line 16
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["manualLinks"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
                    // line 17
                    echo "        <li><a href=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["file"], "manualURL", []), "html", null, true);
                    echo "\" target=\"_blank\" class=\"external\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["file"], "file", []), "html", null, true);
                    echo "</a></li>
      ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 19
                echo "    </ul>
  ";
            }
            // line 21
            echo "  </div>
";
        }
        // line 23
        if ($this->getAttribute(($context["this"] ?? null), "isBroken", [], "method")) {
            // line 24
            echo "  <div class=\"alert alert-danger\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Import has been cancelled."]), "html", null, true);
            echo "</div>
";
        }
        // line 26
        if ($this->getAttribute(($context["this"] ?? null), "hasErrorsOrWarnings", [], "method")) {
            // line 27
            echo "  <div class=\"download-errors\">
      <a href=\"";
            // line 28
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "import", "getErrorsFile"]), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Download error file"]), "html", null, true);
            echo "</a>
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/import/parts/failed.errors.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 28,  91 => 27,  89 => 26,  83 => 24,  81 => 23,  77 => 21,  73 => 19,  62 => 17,  58 => 16,  52 => 14,  49 => 13,  47 => 12,  43 => 11,  40 => 10,  38 => 9,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/import/parts/failed.errors.twig", "");
    }
}
