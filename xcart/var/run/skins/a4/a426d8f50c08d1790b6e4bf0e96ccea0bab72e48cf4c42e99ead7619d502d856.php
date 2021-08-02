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

/* modules/XC/CustomerAttachments/cart_item_info_attachment.twig */
class __TwigTemplate_9db1a82984f8d90754ff9c07056a66092d12b1df4fd75483c91bcd9b3dc414c0 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "isCustomerAttachable", [], "method")) {
            // line 5
            echo "  <p class=\"item-attachment\">
      <input type=\"hidden\" name=\"item_id\" value=\"";
            // line 6
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getItemId", [], "method"), "html", null, true);
            echo "\" />
      <a class=\"customer-attachment\" href=\"cart.php?target=customer_attachments&item_id=";
            // line 7
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "item", []), "getItemId", [], "method"), "html", null, true);
            echo "\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Attach files"]), "html", null, true);
            echo "</a>
      ";
            // line 8
            if ($this->getAttribute(($context["this"] ?? null), "getAttachedFilesQuantity", [], "method")) {
                // line 9
                echo "        <span class=\"files-attached\">";
                echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["X file(s) attached", ["quantity" => $this->getAttribute(($context["this"] ?? null), "getAttachedFilesQuantity", [], "method")]]);
                echo "</span>
      ";
            }
            // line 11
            echo "      ";
            if ($this->getAttribute(($context["this"] ?? null), "isAttachmentNeed", [], "method")) {
                // line 12
                echo "        <span>";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["You have to attach file"]), "html", null, true);
                echo "</span>
      ";
            }
            // line 14
            echo "  </p>
";
        }
    }

    public function getTemplateName()
    {
        return "modules/XC/CustomerAttachments/cart_item_info_attachment.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 14,  56 => 12,  53 => 11,  47 => 9,  45 => 8,  39 => 7,  35 => 6,  32 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/CustomerAttachments/cart_item_info_attachment.twig", "/mff/xcart/skins/customer/modules/XC/CustomerAttachments/cart_item_info_attachment.twig");
    }
}
