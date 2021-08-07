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

/* /mff/xcart/skins/admin/modules/Amazon/PayWithAmazon/method_style.twig */
class __TwigTemplate_0e5d8155dffd7c283be28164ce3ab704aa666621c3b6706fd223ee63e36fa472 extends \XLite\Core\Templating\Twig\Template
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
<style type=\"text/css\">
.module-name-Amazon_PayWithAmazon .payment-logo img {
    margin-top: 7px;
}
</style>";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/Amazon/PayWithAmazon/method_style.twig";
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
        return new Source("", "/mff/xcart/skins/admin/modules/Amazon/PayWithAmazon/method_style.twig", "");
    }
}
