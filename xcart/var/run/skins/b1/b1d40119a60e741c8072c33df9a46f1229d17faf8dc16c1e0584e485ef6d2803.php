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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/CustomerAttachments/parts/files_table.twig */
class __TwigTemplate_dba087a5bbcc0bb8a073c12a358b0f854df6f4939528571f033997cc94c26e98 extends \XLite\Core\Templating\Twig\Template
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
<div>
    ";
        // line 8
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "customerAttachments", [])) {
            // line 9
            echo "      <ul class=\"customer-attachments\">
          ";
            // line 10
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getCustomerAttachments", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["attachment"]) {
                // line 11
                echo "            <li class=\"attachment\">
                ";
                // line 12
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\CustomerAttachments\\View\\AttachmentItem", "attachment" => $context["attachment"], "itemId" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getItemId", [], "method")]]), "html", null, true);
                echo "
            </li>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['attachment'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 15
            echo "      </ul>
    ";
        }
        // line 17
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/CustomerAttachments/parts/files_table.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 17,  55 => 15,  46 => 12,  43 => 11,  39 => 10,  36 => 9,  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/XC/CustomerAttachments/parts/files_table.twig", "");
    }
}
