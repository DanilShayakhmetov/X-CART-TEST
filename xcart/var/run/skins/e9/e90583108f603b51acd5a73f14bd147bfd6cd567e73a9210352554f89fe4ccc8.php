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

/* /mff/xcart/skins/admin/modules/XC/SagePay/sagepay_description.twig */
class __TwigTemplate_3b19c20896a98dc7b3da7ca7a3bd3e0e7fa0c531e40d3c5221b3297f7b70711d extends \XLite\Core\Templating\Twig\Template
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
        if (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "method", []), "getServiceName", [], "method") == "Opayo form protocol")) {
            // line 8
            echo "  <div class=\"description\">
  ";
            // line 9
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["SagePay Form admin description"]);
            echo "
  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/XC/SagePay/sagepay_description.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/XC/SagePay/sagepay_description.twig", "");
    }
}
