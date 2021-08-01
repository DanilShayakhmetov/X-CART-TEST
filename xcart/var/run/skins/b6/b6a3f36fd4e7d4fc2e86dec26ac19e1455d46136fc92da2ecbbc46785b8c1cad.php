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

/* /home/ruslan/Projects/next/output/xcart/src/skins/mail/common/modules/CDev/PINCodes/invoice_item_pin_codes.twig */
class __TwigTemplate_c067d65bf5ddf35fb67bef09981e63cc1487b37d3b87c9da7b0fe6b47d6b1bb0 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "isCustomerInterface", [], "method")) {
            // line 8
            echo "  ";
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getSoldPinCodes", [], "method")) {
                // line 9
                echo "  <li class=\"pin-codes separated\">
    <span class=\"name\">";
                // line 10
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["PIN"]), "html", null, true);
                echo ":</span><br />
    ";
                // line 11
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getSoldPinCodes", [], "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["pin"]) {
                    // line 12
                    echo "      <div class=\"code\">
        ";
                    // line 13
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["pin"], "code", []), "html", null, true);
                    echo "
      </div>
    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pin'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 16
                echo "  </li>
  ";
            }
        } else {
            // line 19
            echo "  ";
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "countPinCodes", [], "method")) {
                // line 20
                echo "  <li class=\"pin-codes separated\">
    <span class=\"name\">";
                // line 21
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["PIN"]), "html", null, true);
                echo ":</span><br />
    ";
                // line 22
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getPinCodes", [], "method"));
                foreach ($context['_seq'] as $context["_key"] => $context["pin"]) {
                    // line 23
                    echo "      <div class=\"code\">
        ";
                    // line 24
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["pin"], "code", []), "html", null, true);
                    echo "
      </div>
    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pin'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 27
                echo "  </li>
  ";
            }
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/mail/common/modules/CDev/PINCodes/invoice_item_pin_codes.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 27,  83 => 24,  80 => 23,  76 => 22,  72 => 21,  69 => 20,  66 => 19,  61 => 16,  52 => 13,  49 => 12,  45 => 11,  41 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/mail/common/modules/CDev/PINCodes/invoice_item_pin_codes.twig", "");
    }
}
