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

/* /mff/xcart/skins/admin/export/parts/failed.buttons.export.twig */
class __TwigTemplate_a8638b1544fcb75f5aa3605fb20056c49e939d6b486bd43382caa87716549a91 extends \XLite\Core\Templating\Twig\Template
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
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Button\\Link", "location" => call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "export"]), "label" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["New Export"]), "style" => "main-button"]]), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/export/parts/failed.buttons.export.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/export/parts/failed.buttons.export.twig", "");
    }
}
