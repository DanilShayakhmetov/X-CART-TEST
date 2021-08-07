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

/* layout/header/mobile_header_parts/slidebar_menu.twig */
class __TwigTemplate_f6092eac51d36879cdbb618a62b97c8267d091daf0890cad479ed19a7b5a0ad8 extends \XLite\Core\Templating\Twig\Template
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
<li class=\"dropdown mobile_header-slidebar\">
  <a id=\"main_menu\" href=\"#slidebar\" class=\"icon-menu\"></a>
</li>";
    }

    public function getTemplateName()
    {
        return "layout/header/mobile_header_parts/slidebar_menu.twig";
    }

    public function getDebugInfo()
    {
        return array (  30 => 4,);
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
 # Header right box
 #}

<li class=\"dropdown mobile_header-slidebar\">
  <a id=\"main_menu\" href=\"#slidebar\" class=\"icon-menu\"></a>
</li>", "layout/header/mobile_header_parts/slidebar_menu.twig", "/mff/xcart/skins/crisp_white/customer/layout/header/mobile_header_parts/slidebar_menu.twig");
    }
}
