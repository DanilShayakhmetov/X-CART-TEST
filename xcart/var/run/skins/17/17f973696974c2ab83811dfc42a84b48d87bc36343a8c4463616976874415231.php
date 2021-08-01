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

/* product/search/simple_form.twig */
class __TwigTemplate_cac3138746a53ddbb48f8970799274e52926506672a0bf888cbfb2a3dd1f1d6c extends \XLite\Core\Templating\Twig\Template
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
<div class=\"simple-search-product-form\">

  ";
        // line 7
        $this->startForm("\\XLite\\View\\Form\\Product\\Search\\Customer\\SimpleForm");        // line 8
        echo "    ";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "product.simple-search-form.simple-box"]]), "html", null, true);
        echo "
  ";
        $this->endForm();        // line 10
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "product/search/simple_form.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 10,  36 => 8,  35 => 7,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "product/search/simple_form.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/product/search/simple_form.twig");
    }
}
