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

/* /mff/xcart/skins/customer/modules/XC/Reviews/reviews_page/reviews.twig */
class __TwigTemplate_d4ab59f58c9cf6abb566c090861b038403e7ca400ec24fd0384838c2cce15319 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"reviews\">

  ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "getPageData", [], "method")) {
            // line 10
            echo "    <ul class=\"reviews-list\">
      ";
            // line 11
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getPageData", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["review"]) {
                // line 12
                echo "        <li class=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getClass", [0 => $context["review"]], "method"), "html", null, true);
                echo "\">
          ";
                // line 13
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "reviews.review", "review" => $context["review"]]]), "html", null, true);
                echo "
        </li>
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['review'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 16
            echo "    </ul>
  ";
        }
        // line 18
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XC/Reviews/reviews_page/reviews.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 18,  58 => 16,  49 => 13,  44 => 12,  40 => 11,  37 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/XC/Reviews/reviews_page/reviews.twig", "");
    }
}
