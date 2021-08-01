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

/* /home/ruslan/Projects/next/output/xcart/src/skins/customer/jscontainer/parts/register_resources.twig */
class __TwigTemplate_bdb9f1d0a323479ab0d033a8f15d412737dcc5c276ad7b412fb69fd0ab90e8b6 extends \XLite\Core\Templating\Twig\Template
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
        return "/home/ruslan/Projects/next/output/xcart/src/skins/customer/jscontainer/parts/register_resources.twig";
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
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/customer/jscontainer/parts/register_resources.twig", "");
    }
}
