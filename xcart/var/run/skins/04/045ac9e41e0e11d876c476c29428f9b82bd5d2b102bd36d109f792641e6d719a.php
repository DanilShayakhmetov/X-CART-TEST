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

/* /mff/xcart/skins/admin/modules/XC/ThemeTweaker/images_settings/parts/custom_images.twig */
class __TwigTemplate_e2d2d7e1c8c907f12ba33f62f7dac764e21d66ea32a5869c8d9b5fb62cf316f3 extends \XLite\Core\Templating\Twig\Template
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
<li>
  <div class=\"custom-images\">

    <h2>";
        // line 10
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Custom images"]), "html", null, true);
        echo "</h2>

    <div class=\"new-image\">
      <div>
        <input id=\"new_images\" class=\"inputfile\" type=\"file\" name=\"new_images[]\" multiple />
        <label for=\"new_images\" class=\"input-button\">";
        // line 15
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Upload image"]), "html", null, true);
        echo "</label>
        <span class=\"input-filename\"></span>
      </div>
    </div>

    ";
        // line 20
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "\\XLite\\Module\\XC\\ThemeTweaker\\View\\Images"]]), "html", null, true);
        echo "

  </div>
</li>";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/admin/modules/XC/ThemeTweaker/images_settings/parts/custom_images.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 20,  44 => 15,  36 => 10,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/admin/modules/XC/ThemeTweaker/images_settings/parts/custom_images.twig", "");
    }
}
