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

/* common/price_plain_body.twig */
class __TwigTemplate_efa51053119a4c41d377205def9074ad5cd321cbeb33bd6461e19c5dcbd963a2 extends \XLite\Core\Templating\Twig\Template
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
";
        // line 5
        if ($this->getAttribute(($context["this"] ?? null), "displayOnlyPrice", [])) {
            // line 6
            echo "    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("common/price_plain_only.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate("common/price_plain_only.twig", "common/price_plain_body.twig", 6)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
        } else {
            // line 8
            echo "    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("common/price_plain.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate("common/price_plain.twig", "common/price_plain_body.twig", 8)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
        }
    }

    public function getTemplateName()
    {
        return "common/price_plain_body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 8,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "common/price_plain_body.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/common/price_plain_body.twig");
    }
}
