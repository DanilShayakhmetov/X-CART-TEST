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

/* modules/XC/Onboarding/wizard_steps/product_added/body.twig */
class __TwigTemplate_0d931e84b35d888857697453f5a6d50d055a48280ed6277f36c333a30cd5d6c0 extends \XLite\Core\Templating\Twig\Template
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
        echo "
<div
  class=\"onboarding-wizard-step step-";
        // line 6
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStepIndex", [], "method"), "html", null, true);
        echo "\"
  v-show=\"isCurrentStep('";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStepIndex", [], "method"), "html", null, true);
        echo "')\"
  :transition=\"stepTransition\">
  <xlite-wizard-step-product-added inline-template :demo-catalog=\"";
        // line 9
        echo (($this->getAttribute(($context["this"] ?? null), "isDemoCatalogAvailable", [], "method")) ? ("true") : ("false"));
        echo "\" product-url-base=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getStorefrontUrlBase", [], "method"), "html", null, true);
        echo "\">

    <div class=\"step-contents demo-catalog-no-product\" v-if=\"!productId && demoCatalog && !isDeleted\">
      <h2 class=\"heading\">";
        // line 12
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Ready to delete demo products?"]), "html", null, true);
        echo "</h2>
      <div class=\"demo-products-showcase\">
        ";
        // line 14
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "modules/XC/Onboarding/images/3_products.svg"]);
        echo "
      </div>
      <div class=\"buttons\">
        ";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Proceed to the next step"]), "style" => "regular-main-button", "attributes" => ["@click" => "skipStep"], "jsCode" => "null;"]]), "html", null, true);
        echo "
        ";
        // line 18
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Delete Demo products"]), "attributes" => ["@click" => "deleteDemoCatalog"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      </div>
    </div>

    <div class=\"step-contents demo-catalog\" v-if=\"productId && demoCatalog && !isDeleted\">
      <h2 class=\"heading\">";
        // line 23
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["You have added 1 product"]), "html", null, true);
        echo "</h2>
      <p class=\"text\">";
        // line 24
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["The newly created product is already in your [storefront]. Have a look! Ready to delete demo products?", ["storefront" => $this->getAttribute(($context["this"] ?? null), "getStorefrontUrl", [], "method")]]);
        echo "</p>
      <div class=\"demo-products-showcase\">
        ";
        // line 26
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "modules/XC/Onboarding/images/3_products.svg"]);
        echo "
      </div>
      <div class=\"buttons\">
        ";
        // line 29
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Proceed to the next step"]), "style" => "regular-main-button", "attributes" => ["@click" => "skipStep"], "jsCode" => "null;"]]), "html", null, true);
        echo "
        ";
        // line 30
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Delete Demo products"]), "attributes" => ["@click" => "deleteDemoCatalog"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      </div>
    </div>

    <div class=\"step-contents no-demo-catalog\" v-if=\"productId && !demoCatalog\">
      <h2 class=\"heading\">";
        // line 35
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["You have added 1 product"]), "html", null, true);
        echo "</h2>
      <p class=\"text\">";
        // line 36
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["You can view the newly created product in your [storefront] or add a few [more products] to your catalog.", ["storefront" => $this->getAttribute(($context["this"] ?? null), "getStorefrontUrl", [], "method"), "more" => $this->getAttribute(($context["this"] ?? null), "getProductListUrl", [], "method")]]);
        echo "</p>
      <div class=\"product-added-image\">
        ";
        // line 38
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "modules/XC/Onboarding/images/add-product.svg"]);
        echo "
      </div>
      <div class=\"buttons\">
      ";
        // line 41
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Proceed to the next step"]), "style" => "regular-main-button", "attributes" => ["@click" => "goToNextStep"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      </div>
    </div>

    <div class=\"step-contents catalog-deleted\" v-if=\"demoCatalog && isDeleted\">
      <div class=\"catalog-deleted-image\">
        ";
        // line 47
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "modules/XC/Onboarding/images/catalog-deleted.svg"]);
        echo "
      </div>
      <h2 class=\"heading\">";
        // line 49
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Done! We ve just deleted all your demo products"]), "html", null, true);
        echo "</h2>
      <p class=\"text\">";
        // line 50
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["Add a few [more products] to your catalog or proceed to the next step - whatever makes you happy.", ["more" => $this->getAttribute(($context["this"] ?? null), "getProductListUrl", [], "method")]]);
        echo "</p>

      <div class=\"buttons\">
      ";
        // line 53
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Regular", "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Proceed to the next step"]), "style" => "regular-main-button", "attributes" => ["@click" => "goToNextStep"], "jsCode" => "null;"]]), "html", null, true);
        echo "
      </div>
    </div>

  </xlite-wizard-step-product-added>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/XC/Onboarding/wizard_steps/product_added/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  140 => 53,  134 => 50,  130 => 49,  125 => 47,  116 => 41,  110 => 38,  105 => 36,  101 => 35,  93 => 30,  89 => 29,  83 => 26,  78 => 24,  74 => 23,  66 => 18,  62 => 17,  56 => 14,  51 => 12,  43 => 9,  38 => 7,  34 => 6,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/XC/Onboarding/wizard_steps/product_added/body.twig", "/mff/xcart/skins/admin/modules/XC/Onboarding/wizard_steps/product_added/body.twig");
    }
}
