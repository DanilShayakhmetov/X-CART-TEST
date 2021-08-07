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

/* /mff/xcart/skins/customer/header/parts/layout_options.twig */
class __TwigTemplate_b3d80340d91ba5b84fdb2716970024980992e46915b55e0a4a6341aee7fa9cdb extends \XLite\Core\Templating\Twig\Template
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
<script>
  var xliteLayoutOptions = {
    cloud_zoom: ";
        // line 9
        echo (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "layout", []), "isCloudZoomAllowed", [], "method")) ? ("true") : ("false"));
        echo ",
    lazy_load: ";
        // line 10
        echo (($this->getAttribute($this->getAttribute(($context["this"] ?? null), "layout", []), "isLazyLoadEnabled", [], "method")) ? ("true") : ("false"));
        echo ",
  };
</script>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/header/parts/layout_options.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 10,  35 => 9,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/header/parts/layout_options.twig", "");
    }
}
