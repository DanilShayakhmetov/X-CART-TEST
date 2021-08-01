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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/import/parts/common.errors.twig */
class __TwigTemplate_254a7b343b046526546098a80d28e8585211355e87cc67b659b5b6c13b3ffa78 extends \XLite\Core\Templating\Twig\Template
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
        // line 7
        echo "
<p>";
        // line 8
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTitle", [], "method"), "html", null, true);
        echo "</p>
";
        // line 9
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getFiles", [], "method"));
        foreach ($context['_seq'] as $context["_key"] => $context["file"]) {
            // line 10
            echo "  <div class=\"errors-wrapper faded initial\">
    <ul class=\"errors\">
      <li class=\"title\">
        ";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["file"], "file", []), "html", null, true);
            echo "
        ";
            // line 14
            if ($this->getAttribute($context["file"], "countW", [])) {
                // line 15
                echo "          <span class=\"count-w\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["file"], "countW", []), "html", null, true);
                echo "</span>
        ";
            }
            // line 17
            echo "        ";
            if ($this->getAttribute($context["file"], "countE", [])) {
                // line 18
                echo "          <span class=\"count-e\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["file"], "countE", []), "html", null, true);
                echo "</span>
        ";
            }
            // line 20
            echo "      </li>
      ";
            // line 21
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getErrorsGroups", [0 => $this->getAttribute($context["file"], "file", [])], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["errorGroup"]) {
                // line 22
                echo "        <li class=\"clearfix type-";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["errorGroup"], "type", []), "html", null, true);
                echo "\">
          <div class=\"message\">
            <div class=\"message-text\">";
                // line 24
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getGroupErrorMessage", [0 => $context["errorGroup"]], "method"), "html", null, true);
                echo "</div>
            <hr>
            <div class=\"rows\">";
                // line 26
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getGroupErrorRows", [0 => $context["errorGroup"]], "method"), "html", null, true);
                echo "</div>
          </div>
          <div class=\"text\">";
                // line 28
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getErrorText", [0 => $context["errorGroup"]], "method"), "html", null, true);
                echo "</div>
        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['errorGroup'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 31
            echo "    </ul>
  </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['file'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/import/parts/common.errors.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  99 => 31,  90 => 28,  85 => 26,  80 => 24,  74 => 22,  70 => 21,  67 => 20,  61 => 18,  58 => 17,  52 => 15,  50 => 14,  46 => 13,  41 => 10,  37 => 9,  33 => 8,  30 => 7,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/import/parts/common.errors.twig", "");
    }
}
