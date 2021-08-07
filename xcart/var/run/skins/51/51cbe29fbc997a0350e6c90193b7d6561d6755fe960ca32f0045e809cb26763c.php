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

/* modules/XC/FacebookMarketing/pixel_content_data/body.twig */
class __TwigTemplate_c5f8cc329ac4280ef589dea1f18562f5b3ae5c3bc527761fc5305f241ee0e635 extends \XLite\Core\Templating\Twig\Template
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
        echo "<div class=\"fb-pixel-content-data\">
  ";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getPixelData", [], "method")], "method"), "html", null, true);
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/FacebookMarketing/pixel_content_data/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  33 => 5,  30 => 4,);
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
 # Facebook pixel content data
 #}
<div class=\"fb-pixel-content-data\">
  {{ this.displayCommentedData(this.getPixelData()) }}
</div>
", "modules/XC/FacebookMarketing/pixel_content_data/body.twig", "/mff/xcart/skins/customer/modules/XC/FacebookMarketing/pixel_content_data/body.twig");
    }
}
