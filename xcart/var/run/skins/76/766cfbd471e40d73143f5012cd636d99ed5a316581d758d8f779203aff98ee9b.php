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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/welcome_block/root_admin/block.items.twig */
class __TwigTemplate_ab52228e376bae854034b011256b63f846a768c1afa75c49f83fe23de5082830 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"step-items\">
  <ul>
    <li class=\"item-store\">";
        // line 9
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Specify your _store information_", ["URL" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "settings", "", ["page" => "Company"]])]]);
        echo "</li>
    <li class=\"item-products\">";
        // line 10
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Add your _products_", ["URL" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "product_list"])]]);
        echo "</li>
    <li class=\"item-taxes\">";
        // line 11
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Setup _address zones_ and _taxes_", ["URL1" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "zones"]), "URL2" => $this->getAttribute(($context["this"] ?? null), "getTaxesURL", [], "method")]]);
        echo "</li>
    <li class=\"item-shipping\">";
        // line 12
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Configure _shipping methods_", ["URL" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "shipping_methods"])]]);
        echo "</li>
    <li class=\"item-payment\">";
        // line 13
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Choose _payment methods_", ["URL" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "payment_settings"])]]);
        echo "</li>
    ";
        // line 14
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "admin-welcome-items"]]), "html", null, true);
        echo "
  </ul>
</div>


";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/welcome_block/root_admin/block.items.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 14,  51 => 13,  47 => 12,  43 => 11,  39 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/welcome_block/root_admin/block.items.twig", "");
    }
}
