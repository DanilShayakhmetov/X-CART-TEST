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

/* /home/ruslan/Projects/next/output/xcart/src/skins/admin/order/history/parts/event_details_author.twig */
class __TwigTemplate_6e4b0ed092b450d5aa7bcf1b8ea07ffd83b87d0fa11e604f73d97dd17d8b5e6d extends \XLite\Core\Templating\Twig\Template
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
        echo "<li class=\"author\">
  ";
        // line 7
        if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "showAuthor", [], "method")) {
            // line 8
            echo "    ";
            if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthor", [], "method")) {
                // line 9
                echo "      <a href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "profile", "", ["profile_id" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthor", [], "method"), "getProfileId", [], "method")]]), "html", null, true);
                echo "\"
         data-toggle=\"popover\"
         data-placement=\"top\"
         data-trigger=\"hover\"
         data-content=\"IP: ";
                // line 13
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthorIp", [], "method"), "html", null, true);
                echo "\">
        ";
                // line 14
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthor", [], "method"), "getLogin", [], "method"), "html", null, true);
                echo "
      </a>
    ";
            } else {
                // line 17
                echo "      ";
                if ($this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthorName", [], "method")) {
                    // line 18
                    echo "        <span class=\"removed-profile-name\"
              data-toggle=\"popover\"
              data-placement=\"top\"
              data-trigger=\"hover\"
              data-content=\"IP: ";
                    // line 22
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthorIp", [], "method"), "html", null, true);
                    echo "\">
          ";
                    // line 23
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthorName", [], "method"), "html", null, true);
                    echo "
        </span>
      ";
                } else {
                    // line 26
                    echo "        <span class=\"no-author\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "event", []), "getAuthorIp", [], "method"), "html", null, true);
                    echo "</span>
      ";
                }
                // line 28
                echo "    ";
            }
            // line 29
            echo "  ";
        }
        // line 30
        echo "</li>
";
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/history/parts/event_details_author.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 30,  84 => 29,  81 => 28,  75 => 26,  69 => 23,  65 => 22,  59 => 18,  56 => 17,  50 => 14,  46 => 13,  38 => 9,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/admin/order/history/parts/event_details_author.twig", "");
    }
}
