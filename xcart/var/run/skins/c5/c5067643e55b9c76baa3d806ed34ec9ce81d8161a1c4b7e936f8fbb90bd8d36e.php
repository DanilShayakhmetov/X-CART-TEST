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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/checkout/steps/review/parts/items.includedModifiers.twig */
class __TwigTemplate_c4db9c45a501e8c386a811c8c8a83a1073e0613e2b9a993824f6cc2e2f97279f extends \XLite\Core\Templating\Twig\Template
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
<ul class=\"modifiers included-modifiers\">
  ";
        // line 8
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getItemsIncludeSurchargesTotals", [], "method")) {
            // line 9
            echo "    ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getItemsIncludeSurchargesTotals", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
                // line 10
                echo "      <li class='included-surcharge type-";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["row"], "surcharge", []), "getType", [], "method"), "html", null, true);
                echo "-surcharge code-";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["row"], "surcharge", []), "getCode", [], "method"), "html", null, true);
                echo "-surcharge'>
        <span class=\"name\">";
                // line 11
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Including X", ["name" => $this->getAttribute($this->getAttribute($context["row"], "surcharge", []), "getName", [], "method")]]), "html", null, true);
                echo ":</span>
          <span class=\"value\">";
                // line 12
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Surcharge", "surcharge" => $this->getAttribute($context["row"], "cost", []), "currency" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "cart", []), "getCurrency", [], "method")]]), "html", null, true);
                echo "</span>
      </li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 15
            echo "  ";
        }
        // line 16
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/checkout/steps/review/parts/items.includedModifiers.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 16,  61 => 15,  52 => 12,  48 => 11,  41 => 10,  36 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/checkout/steps/review/parts/items.includedModifiers.twig", "");
    }
}
