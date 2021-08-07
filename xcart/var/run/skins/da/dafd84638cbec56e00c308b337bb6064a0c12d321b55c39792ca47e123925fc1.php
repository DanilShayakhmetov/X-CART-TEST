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

/* /mff/xcart/skins/admin/email_settings/queues_note.twig */
class __TwigTemplate_918301e99911aa91622e03019a804aca5401a7efdb62f8d074c9c5190d6fa032 extends \XLite\Core\Templating\Twig\Template
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
        if ((($this->getAttribute($this->getAttribute(($context["this"] ?? null), "field", []), "name", []) == "smtp_section") && $this->getAttribute(($context["this"] ?? null), "isQueuesNoteVisible", [], "method"))) {
            // line 8
            echo "    <div class=\"alert alert-info\">";
            echo call_user_func_array($this->env->getFunction('t')->getCallable(), ["To send email asynchronously and improve website performance we recommend to set up queues in on your server. [Learn how to do it].", ["url" => "https://kb.x-cart.com/en/general_setup/configuring_queues.html"]]);
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/email_settings/queues_note.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/email_settings/queues_note.twig", "");
    }
}
