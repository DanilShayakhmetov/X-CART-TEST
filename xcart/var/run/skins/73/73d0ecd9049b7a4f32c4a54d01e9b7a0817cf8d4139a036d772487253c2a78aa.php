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

/* /home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/signin/parts/login.twig */
class __TwigTemplate_3e637642b2c7f697fd311202159cddc79465e13a76cd1480ef4f62f56f0f4e09 extends \XLite\Core\Templating\Twig\Template
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
<div class=\"signin-login-wrapper\">
  ";
        $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("signin/parts/login/heading.twig");        list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
        if ($templateWrapperText) {
echo $templateWrapperStart;
}

        // line 8
        $this->loadTemplate("signin/parts/login/heading.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/signin/parts/login.twig", 8)->display($context);
        if ($templateWrapperText) {
            echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
        }
        // line 9
        echo "  ";
        if ( !$this->getAttribute(($context["this"] ?? null), "isRegisterMode", [], "method")) {
            // line 10
            echo "    <div class=\"login-box\">
      ";
            // line 11
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\Module\\XC\\CrispWhiteSkin\\View\\CheckoutAuthorization"]]), "html", null, true);
            echo "
    </div>
  ";
        } else {
            // line 14
            echo "    <div class=\"register-box\">
      ";
            // line 15
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\View\\Model\\Profile\\Main", "useBodyTemplate" => "1"]]), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 18
        echo "</div>";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/signin/parts/login.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  65 => 18,  59 => 15,  56 => 14,  50 => 11,  47 => 10,  44 => 9,  39 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/signin/parts/login.twig", "");
    }
}
