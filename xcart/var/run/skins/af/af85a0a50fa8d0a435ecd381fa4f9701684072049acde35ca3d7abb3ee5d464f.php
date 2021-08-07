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

/* /mff/xcart/skins/mail/common/failed_transaction/parts/transaction_url.twig */
class __TwigTemplate_79c66c0dc193a50072664e82b19ad75c744b5e6bbc86b1ff2a5322cf160030cf extends \XLite\Core\Templating\Twig\Template
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
<p>
  ";
        // line 8
        echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["You can find the failed transaction here", ["url" => $this->getAttribute(($context["this"] ?? null), "transactionSearchURL", [])]]);
        echo "
<p>";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/mail/common/failed_transaction/parts/transaction_url.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 8,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/mail/common/failed_transaction/parts/transaction_url.twig", "");
    }
}
