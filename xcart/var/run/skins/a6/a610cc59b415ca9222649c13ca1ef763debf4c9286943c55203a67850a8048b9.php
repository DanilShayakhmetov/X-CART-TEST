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

/* modules/Kliken/GoogleAds/verification-token.twig */
class __TwigTemplate_1310aea900c0e27ef449c9fee398d4a8a08699a8856d8f7d8f2e50a0ca38649a extends \XLite\Core\Templating\Twig\Template
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
        $context["googleToken"] = $this->getAttribute(($context["this"] ?? null), "getGoogleVerificationToken", [], "method");
        // line 6
        if (($context["googleToken"] ?? null)) {
            // line 7
            echo "    <meta name=\"google-site-verification\" content=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["googleToken"] ?? null), "html", null, true);
            echo "\" />
";
        }
    }

    public function getTemplateName()
    {
        return "modules/Kliken/GoogleAds/verification-token.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "modules/Kliken/GoogleAds/verification-token.twig", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/modules/Kliken/GoogleAds/verification-token.twig");
    }
}
