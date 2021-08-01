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

/* /home/ruslan/Projects/next/output/xcart/src/skins/common/file_uploader/parts/menu.alt.twig */
class __TwigTemplate_d11af92c79a483d6109a2289d98950cdcbf655a47538518fcdfe587db8382928 extends \XLite\Core\Templating\Twig\Template
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
        // line 8
        echo "
";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "hasAlt", [], "method")) {
            // line 10
            echo "  <li role=\"presentation\" class=\"alt-text\">
    <div class=\"value\" @click.prevent.stop=\"showAlt\">
      ";
            // line 12
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Alt"]), "html", null, true);
            echo ":
      <span>";
            // line 13
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAlt", [], "method"), "html", null, true);
            echo "</span>
    </div>
    <div class=\"input-group\">
      <span class=\"input-group-addon\">";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Alt"]), "html", null, true);
            echo ":</span>
      <input name=\"";
            // line 17
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getName", [], "method"), "html", null, true);
            echo "[alt]\" v-model=\"alt\"
             placeholder=\"";
            // line 18
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Enter alt text"]), "html", null, true);
            echo "\"
             value=\"";
            // line 19
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAlt", [], "method"), "html", null, true);
            echo "\" class=\"form-control input-alt\"
             @change=\"doChangeAlt\"
             @keydown=\"doChangeAlt\"
             @blur=\"doChangeAlt\"/>
    </div>
  </li>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/common/file_uploader/parts/menu.alt.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 19,  57 => 18,  53 => 17,  49 => 16,  43 => 13,  39 => 12,  35 => 10,  33 => 9,  30 => 8,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/common/file_uploader/parts/menu.alt.twig", "");
    }
}
