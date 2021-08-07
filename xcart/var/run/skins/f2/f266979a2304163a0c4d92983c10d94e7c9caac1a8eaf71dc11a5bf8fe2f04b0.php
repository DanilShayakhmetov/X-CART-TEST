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

/* /mff/xcart/skins/admin/modules/CDev/PINCodes/inv_track_selector.twig */
class __TwigTemplate_0046c30bbc04535781bc8ae20a7e9233aea1e68e069ef4ac164f977678b38104 extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "hasManualPinCodes", [], "method")) {
            // line 8
            echo "  <tr>
    <td>";
            // line 9
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Inventory tracking for this product is"]), "html", null, true);
            echo "</td>
    <td>";
            // line 10
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Enabled"]), "html", null, true);
            echo " (";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Can not be disabled for products with manually defined PIN codes"]), "html", null, true);
            echo ")</td>
  </tr>
";
        }
        // line 13
        echo "
";
        // line 14
        if ( !$this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "hasManualPinCodes", [], "method")) {
            // line 15
            echo "  ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("product/inventory/inv_track_selector.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate("product/inventory/inv_track_selector.twig", "/mff/xcart/skins/admin/modules/CDev/PINCodes/inv_track_selector.twig", 15)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/CDev/PINCodes/inv_track_selector.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 15,  53 => 14,  50 => 13,  42 => 10,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/CDev/PINCodes/inv_track_selector.twig", "");
    }
}
