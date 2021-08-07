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

/* modules/XC/ThemeTweaker/themetweaker_panel/modal/modal.twig */
class __TwigTemplate_b24b9e3ffcbe391923133b60cb056a97c38fd6b54826e2d941074e67fb960202 extends \XLite\Core\Templating\Twig\Template
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
<script type=\"x/template\" id=\"themetweaker-modal-template\">
  <div class=\"themetweaker-modal-mask\" v-show=\"show\" transition=\"modal\" :data-namespace=\"namespace\">
    <div class=\"themetweaker-modal-wrapper\">
      <div class=\"themetweaker-modal-container\">

        <div class=\"themetweaker-modal-header\">
          <slot name=\"header\">
            <a class=\"themetweaker-modal-close\"
                    @click=\"sendEvent('cancel')\">
              ";
        // line 14
        echo call_user_func_array($this->env->getFunction('svg')->getCallable(), [$this->env, $context, "images/close.svg", "common"]);
        echo "
            </a>
          </slot>
        </div>

        <div class=\"themetweaker-modal-body\">
          <slot name=\"body\">
          </slot>
        </div>

        <div class=\"themetweaker-modal-footer\">
          <slot name=\"footer\">
            <button class=\"themetweaker-modal-button\"
                    @click=\"sendEvent('ok')\">
              <span>";
        // line 28
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["OK"]), "html", null, true);
        echo "</span>
            </button>
          </slot>
        </div>
      </div>
    </div>
  </div>
</script>
";
    }

    public function getTemplateName()
    {
        return "modules/XC/ThemeTweaker/themetweaker_panel/modal/modal.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 28,  42 => 14,  30 => 4,);
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
 # Layout editor panel
 #}

<script type=\"x/template\" id=\"themetweaker-modal-template\">
  <div class=\"themetweaker-modal-mask\" v-show=\"show\" transition=\"modal\" :data-namespace=\"namespace\">
    <div class=\"themetweaker-modal-wrapper\">
      <div class=\"themetweaker-modal-container\">

        <div class=\"themetweaker-modal-header\">
          <slot name=\"header\">
            <a class=\"themetweaker-modal-close\"
                    @click=\"sendEvent('cancel')\">
              {{ svg('images/close.svg', 'common')|raw }}
            </a>
          </slot>
        </div>

        <div class=\"themetweaker-modal-body\">
          <slot name=\"body\">
          </slot>
        </div>

        <div class=\"themetweaker-modal-footer\">
          <slot name=\"footer\">
            <button class=\"themetweaker-modal-button\"
                    @click=\"sendEvent('ok')\">
              <span>{{ t('OK') }}</span>
            </button>
          </slot>
        </div>
      </div>
    </div>
  </div>
</script>
", "modules/XC/ThemeTweaker/themetweaker_panel/modal/modal.twig", "/mff/xcart/skins/customer/modules/XC/ThemeTweaker/themetweaker_panel/modal/modal.twig");
    }
}
