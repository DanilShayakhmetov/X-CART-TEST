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

/* layout/content/main.center.center.twig */
class __TwigTemplate_706b76ace0197d2685e29a601d037d4f034ea4abab15c66ff3f4493bef8c3a47 extends \XLite\Core\Templating\Twig\Template
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
<div id=\"content\" class=\"column\">
  <div class=\"section\">
    <a id=\"main-content\"></a>
    ";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\ListContainer", "inner" => "layout/content/center.twig", "group" => "center"]]), "html", null, true);
        echo "
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "layout/content/main.center.center.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 10,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("{##
 # Center box
 #
 # @ListChild (list=\"layout.main.center\", weight=\"200\")
 #}

<div id=\"content\" class=\"column\">
  <div class=\"section\">
    <a id=\"main-content\"></a>
    {{ widget('XLite\\\\View\\\\ListContainer', inner='layout/content/center.twig', group='center') }}
  </div>
</div>
", "layout/content/main.center.center.twig", "/mff/xcart/skins/customer/layout/content/main.center.center.twig");
    }
}
