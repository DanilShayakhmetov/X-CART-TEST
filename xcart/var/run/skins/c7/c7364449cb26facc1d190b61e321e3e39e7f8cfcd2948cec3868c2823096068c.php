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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/product/attributes/parts/display_mode.twig */
class __TwigTemplate_bce1495bcf234877e924f0d17c931f1ca441d40d1ddcb6c8a0f56f7fae554d23 extends \XLite\Core\Templating\Twig\Template
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
        $context["isNew"] =  !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "attribute", [], "any", false, true), "entity", [], "any", true, true);
        // line 8
        $context["selectBoxMode"] = twig_constant("XLite\\Model\\Attribute::SELECT_BOX_MODE");
        // line 9
        $context["blocksMode"] = twig_constant("XLite\\Model\\Attribute::BlOCKS_MODE");
        // line 10
        $context["fieldValue"] = ((($context["isNew"] ?? null)) ? (($context["selectBoxMode"] ?? null)) : ($this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "attribute", []), "entity", []), "getDisplayMode", [], "method")));
        // line 11
        $context["name"] = ((($context["isNew"] ?? null)) ? ("newValue[NEW_ID][displayMode]") : ((("displayMode[" . $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "attribute", []), "entity", []), "getId", [], "method")) . "]")));
        // line 12
        echo "
<div class=\"display-mode type-s\">
  <div class=\"title\">
    ";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Display the option values in "]), "html", null, true);
        echo "
    <div>
      <a href=\"#\" class=\"display-mode-link\">";
        // line 17
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, (((($context["fieldValue"] ?? null) === ($context["blocksMode"] ?? null))) ? (call_user_func_array($this->env->getFunction('t')->getCallable(), ["Blocks"])) : (call_user_func_array($this->env->getFunction('t')->getCallable(), ["Selectbox"]))), "html", null, true);
        echo "</a>
      <div class=\"value\">
        <div class=\"display-mode-variant\">
          ";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Radio", "value" =>         // line 22
($context["selectBoxMode"] ?? null), "fieldName" =>         // line 23
($context["name"] ?? null), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Selectbox"]), "fieldId" => (        // line 25
($context["name"] ?? null) . "-selectbox"), "isChecked" => (        // line 26
($context["fieldValue"] ?? null) === ($context["selectBoxMode"] ?? null)), "attributes" => ["class" => "display-mode-input"]]]), "html", null, true);
        // line 28
        echo "
        </div>
        <div class=\"display-mode-variant\">
          ";
        // line 31
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\FormField\\Input\\Radio", "value" =>         // line 33
($context["blocksMode"] ?? null), "fieldName" =>         // line 34
($context["name"] ?? null), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Blocks"]), "fieldId" => (        // line 36
($context["name"] ?? null) . "-blocks"), "isChecked" => (        // line 37
($context["fieldValue"] ?? null) === ($context["blocksMode"] ?? null)), "attributes" => ["class" => "display-mode-input"]]]), "html", null, true);
        // line 39
        echo "
        </div>
      </div>
    </div>
  </div>
</div>

";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/product/attributes/parts/display_mode.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  76 => 39,  74 => 37,  73 => 36,  72 => 34,  71 => 33,  70 => 31,  65 => 28,  63 => 26,  62 => 25,  61 => 23,  60 => 22,  59 => 20,  53 => 17,  48 => 15,  43 => 12,  41 => 11,  39 => 10,  37 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/product/attributes/parts/display_mode.twig", "");
    }
}
