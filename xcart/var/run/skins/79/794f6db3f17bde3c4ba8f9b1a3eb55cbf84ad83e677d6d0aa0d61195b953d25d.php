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

/* jscontainer/parts/register_resources.twig */
class __TwigTemplate_7e426acba309606981d380d2504b0a5fb4ca90f292cdb324a6347926334dd940 extends \XLite\Core\Templating\Twig\Template
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
        echo "<script>
  (function () {
    var resources = ";
        // line 8
        echo $this->getAttribute(($context["this"] ?? null), "getResourceRegistry", [], "method");
        echo ";
    var cacheTimestamp = ";
        // line 9
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getLatestCacheTimestamp", [], "method"), "html", null, true);
        echo "

    if (window.CoreAMD !== undefined) {
      require('js/core', function (core) {
        core.resourcesCacheTimestamp = cacheTimestamp;
        core.registerResources(resources);
        core.htmlResourcesLoadDeferred.resolve();
      });
    } else {
      document.addEventListener('amd-ready', function (event) {
        require('js/core', function (core) {
          core.resourcesCacheTimestamp = cacheTimestamp;
          core.registerResources(resources);
          core.htmlResourcesLoadDeferred.resolve();
        });
      });
    }
  })();
</script>
";
    }

    public function getTemplateName()
    {
        return "jscontainer/parts/register_resources.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 9,  34 => 8,  30 => 6,);
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
 # Cached JS part
 #
 # @ListChild (list=\"jscontainer.js\", weight=\"50\")
 #}
<script>
  (function () {
    var resources = {{ this.getResourceRegistry()|raw }};
    var cacheTimestamp = {{ this.getLatestCacheTimestamp() }}

    if (window.CoreAMD !== undefined) {
      require('js/core', function (core) {
        core.resourcesCacheTimestamp = cacheTimestamp;
        core.registerResources(resources);
        core.htmlResourcesLoadDeferred.resolve();
      });
    } else {
      document.addEventListener('amd-ready', function (event) {
        require('js/core', function (core) {
          core.resourcesCacheTimestamp = cacheTimestamp;
          core.registerResources(resources);
          core.htmlResourcesLoadDeferred.resolve();
        });
      });
    }
  })();
</script>
", "jscontainer/parts/register_resources.twig", "/mff/xcart/skins/customer/jscontainer/parts/register_resources.twig");
    }
}
