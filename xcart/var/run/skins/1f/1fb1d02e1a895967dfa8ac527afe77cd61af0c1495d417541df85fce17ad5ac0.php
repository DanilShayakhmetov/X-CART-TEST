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

/* items_list/model/table_content.twig */
class __TwigTemplate_a13c246308b70fe65f2a24a09c7ceb6ac7f3e79ffb75a7f6ea1547a6c3f4dfbc extends \XLite\Core\Templating\Twig\Template
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
<a name=\"";
        // line 5
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getAnchorName", [], "method"), "html", null, true);
        echo "\" class=\"list-anchor\"></a>
<div ";
        // line 6
        echo $this->getAttribute(($context["this"] ?? null), "getContainerAttributesAsString", [], "method");
        echo ">
  ";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "displayCommentedData", [0 => $this->getAttribute(($context["this"] ?? null), "getItemsListParams", [], "method")], "method"), "html", null, true);
        echo "
  ";
        // line 8
        if ( !$this->getAttribute(($context["this"] ?? null), "hasResults", [], "method")) {
            // line 9
            echo "    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/empty_table_description.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate("items_list/model/empty_table_description.twig", "items_list/model/table_content.twig", 9)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 10
            echo "  ";
        } else {
            // line 11
            echo "    <div class=\"no-items\" style=\"display: none;\"></div>
  ";
        }
        // line 13
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isHeaderVisible", [], "method")) {
            // line 14
            echo "    ";
            ob_start(function () { return ''; });
            // line 15
            echo "      <div class=\"list-header\">
        ";
            // line 16
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header.before", "type" => "inherited"]]), "html", null, true);
            echo "
        ";
            // line 17
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getTopActions", [], "method"));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 18
                echo "          <div class=\"button-container\">";
                $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($context["tpl"]);                list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
                if ($templateWrapperText) {
echo $templateWrapperStart;
}

                $this->loadTemplate($context["tpl"], "items_list/model/table_content.twig", 18)->display($context);
                if ($templateWrapperText) {
                    echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
                }
                echo "</div>
        ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            echo "        ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "header", "type" => "inherited"]]), "html", null, true);
            echo "
      </div>
    ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
            // line 23
            echo "  ";
        }
        // line 24
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isPageBodyVisible", [], "method")) {
            // line 25
            echo "    ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($this->getAttribute(($context["this"] ?? null), "getPageBodyTemplate", [], "method"));            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            $this->loadTemplate($this->getAttribute(($context["this"] ?? null), "getPageBodyTemplate", [], "method"), "items_list/model/table_content.twig", 25)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 26
            echo "  ";
        }
        // line 27
        echo "  ";
        if ($this->getAttribute(($context["this"] ?? null), "isPagerVisible", [], "method")) {
            // line 28
            echo "    <div class=\"table-pager\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["this"] ?? null), "pager", []), "display", [], "method"), "html", null, true);
            echo "</div>
  ";
        }
        // line 30
        echo "
  ";
        // line 31
        if ($this->getAttribute(($context["this"] ?? null), "isFooterVisible", [], "method")) {
            // line 32
            echo "    <div class=\"list-footer\">
      ";
            // line 33
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getBottomActions", [], "method"));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["tpl"]) {
                // line 34
                echo "        <div class=\"button-container\">";
                $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($context["tpl"]);                list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
                if ($templateWrapperText) {
echo $templateWrapperStart;
}

                $this->loadTemplate($context["tpl"], "items_list/model/table_content.twig", 34)->display($context);
                if ($templateWrapperText) {
                    echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
                }
                echo "</div>
      ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tpl'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 36
            echo "      ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "footer", "type" => "inherited"]]), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 39
        echo "
</div>

";
        // line 42
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "itemsList.footer.after"]]), "html", null, true);
        echo "

";
        // line 44
        if ($this->getAttribute(($context["this"] ?? null), "isPanelVisible", [], "method")) {
            // line 45
            echo "  ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => $this->getAttribute(($context["this"] ?? null), "getPanelClass", [], "method")]]), "html", null, true);
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "items_list/model/table_content.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  225 => 45,  223 => 44,  218 => 42,  213 => 39,  206 => 36,  181 => 34,  164 => 33,  161 => 32,  159 => 31,  156 => 30,  150 => 28,  147 => 27,  144 => 26,  133 => 25,  130 => 24,  127 => 23,  120 => 20,  95 => 18,  78 => 17,  74 => 16,  71 => 15,  68 => 14,  65 => 13,  61 => 11,  58 => 10,  47 => 9,  45 => 8,  41 => 7,  37 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/model/table_content.twig", "/mff/xcart/skins/admin/items_list/model/table_content.twig");
    }
}
