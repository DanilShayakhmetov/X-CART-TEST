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

/* items_list/model/table/body.twig */
class __TwigTemplate_9eb40e5e1667d55958eedadbc442de96e87c1529b6d7ef5382578a2cc6bd9866 extends \XLite\Core\Templating\Twig\Template
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
        ob_start(function () { return ''; });
        // line 6
        echo "  <div class=\"table-wrapper ";
        if ( !$this->getAttribute(($context["this"] ?? null), "hasResults", [], "method")) {
            echo "empty";
        }
        echo "\">
    <table class=\"";
        // line 7
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTableTagClassString", [], "method"), "html", null, true);
        echo "\" cellspacing=\"0\">

      ";
        // line 9
        if ($this->getAttribute(($context["this"] ?? null), "isTableHeaderVisible", [], "method")) {
            // line 10
            echo "        <thead>
        <tr>
          ";
            // line 12
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getColumns", [], "method"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
                // line 13
                echo "            ";
                if ( !$this->getAttribute(($context["this"] ?? null), "isNoColumnHead", [0 => $context["column"]], "method")) {
                    // line 14
                    echo "              <th ";
                    if (($this->getAttribute(($context["this"] ?? null), "getColumnHeadColspan", [0 => $context["column"]], "method") > 1)) {
                        echo "colspan=\"";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getColumnHeadColspan", [0 => $context["column"]], "method"), "html", null, true);
                        echo "\"";
                    }
                    echo " class=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getHeadClass", [0 => $context["column"]], "method"), "html", null, true);
                    echo "\">
                ";
                    $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table/parts/head.cell.twig");                    list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
                    if ($templateWrapperText) {
echo $templateWrapperStart;
}

                    // line 15
                    $this->loadTemplate("items_list/model/table/parts/head.cell.twig", "items_list/model/table/body.twig", 15)->display($context);
                    if ($templateWrapperText) {
                        echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
                    }
                    // line 16
                    echo "              </th>
            ";
                }
                // line 18
                echo "          ";
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 19
            echo "        </tr>
        </thead>
      ";
        }
        // line 22
        echo "
      ";
        // line 23
        if ($this->getAttribute(($context["this"] ?? null), "isHeadSearchVisible", [], "method")) {
            // line 24
            echo "        <tbody class=\"head-search\">
        ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table/parts/head_search.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            // line 25
            $this->loadTemplate("items_list/model/table/parts/head_search.twig", "items_list/model/table/body.twig", 25)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 26
            echo "        </tbody>
      ";
        }
        // line 28
        echo "
      ";
        // line 29
        if ($this->getAttribute(($context["this"] ?? null), "isTopInlineCreation", [], "method")) {
            // line 30
            echo "        <tbody class=\"create top-create\">
        ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table/parts/create_box.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            // line 31
            $this->loadTemplate("items_list/model/table/parts/create_box.twig", "items_list/model/table/body.twig", 31)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 32
            echo "        </tbody>
      ";
        }
        // line 34
        echo "
      <tbody class=\"lines\">
      ";
        // line 36
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getPageData", [], "method"));
        foreach ($context['_seq'] as $context["idx"] => $context["entity"]) {
            // line 37
            echo "        <tr ";
            echo $this->getAttribute(($context["this"] ?? null), "printTagAttributes", [0 => $this->getAttribute(($context["this"] ?? null), "getLineAttributes", [0 => $context["idx"], 1 => $context["entity"]], "method")], "method");
            echo ">
          ";
            // line 38
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getColumns", [], "method"));
            foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
                // line 39
                echo "            <td class=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getColumnClass", [0 => $context["column"], 1 => $context["entity"]], "method"), "html", null, true);
                echo "\">
              <div class=\"cell\">
                ";
                // line 41
                if ($this->getAttribute(($context["this"] ?? null), "isTemplateColumnVisible", [0 => $context["column"], 1 => $context["entity"]], "method")) {
                    // line 42
                    echo "                  ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, ["template" => $this->getAttribute($context["column"], "template", []), "idx" => $context["idx"], "entity" => $context["entity"], "column" => $context["column"], "editOnly" => $this->getAttribute($context["column"], "editOnly", []), "viewOnly" => $this->getAttribute(($context["this"] ?? null), "isStatic", [], "method")]]), "html", null, true);
                    echo "
                ";
                }
                // line 44
                echo "                ";
                if ($this->getAttribute(($context["this"] ?? null), "isClassColumnVisible", [0 => $context["column"], 1 => $context["entity"]], "method")) {
                    // line 45
                    echo "                  ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => $this->getAttribute($context["column"], "class", []), "idx" => $context["idx"], "entity" => $context["entity"], "column" => $context["column"], "itemsList" => $this->getAttribute(($context["this"] ?? null), "getSelf", [], "method"), "fieldName" => $this->getAttribute($context["column"], "code", []), "fieldParams" => $this->getAttribute(($context["this"] ?? null), "preprocessFieldParams", [0 => $context["column"], 1 => $context["entity"]], "method"), "editOnly" => $this->getAttribute($context["column"], "editOnly", []), "viewOnly" => $this->getAttribute(($context["this"] ?? null), "isStatic", [], "method")]]), "html", null, true);
                    echo "
                ";
                }
                // line 47
                echo "                ";
                if ($this->getAttribute(($context["this"] ?? null), "isEditLinkEnabled", [0 => $context["column"], 1 => $context["entity"]], "method")) {
                    // line 48
                    echo "                  <div class=\"entity-edit-link\" ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEditLinkAttributes", [0 => $context["entity"], 1 => $context["column"]], "method"), "html", null, true);
                    echo ">
                    <a href=\"";
                    // line 49
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "buildEntityURL", [0 => $context["entity"], 1 => $context["column"]], "method"), "html", null, true);
                    echo "\">
                      ";
                    // line 50
                    if ($this->getAttribute(($context["this"] ?? null), "getEditLinkLabel", [0 => $context["entity"]], "method")) {
                        // line 51
                        echo "                        ";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getEditLinkLabel", [0 => $context["entity"]], "method"), "html", null, true);
                        echo "
                      ";
                    } else {
                        // line 53
                        echo "                        <i class=\"fa fa-edit icon\"></i>
                      ";
                    }
                    // line 55
                    echo "                    </a>
                  </div>
                ";
                }
                // line 58
                echo "                ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => $this->getAttribute(($context["this"] ?? null), "getCellListNamePart", [0 => "cell", 1 => $context["column"]], "method"), "type" => "inherited", "column" => $context["column"], "entity" => $context["entity"]]]), "html", null, true);
                echo "
              </div>
            </td>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 62
            echo "        </tr>
        ";
            // line 63
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => "row", "type" => "inherited", "idx" => $context["idx"], "entity" => $context["entity"]]]), "html", null, true);
            echo "
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['idx'], $context['entity'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 65
        echo "      </tbody>

      ";
        // line 67
        if ($this->getAttribute(($context["this"] ?? null), "isBottomInlineCreation", [], "method")) {
            // line 68
            echo "        <tbody class=\"create bottom-create\">
        ";
            $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath("items_list/model/table/parts/create_box.twig");            list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
            if ($templateWrapperText) {
echo $templateWrapperStart;
}

            // line 69
            $this->loadTemplate("items_list/model/table/parts/create_box.twig", "items_list/model/table/body.twig", 69)->display($context);
            if ($templateWrapperText) {
                echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
            }
            // line 70
            echo "        </tbody>
      ";
        }
        // line 72
        echo "
    </table>
  </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "items_list/model/table/body.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  275 => 72,  271 => 70,  266 => 69,  258 => 68,  256 => 67,  252 => 65,  244 => 63,  241 => 62,  230 => 58,  225 => 55,  221 => 53,  215 => 51,  213 => 50,  209 => 49,  204 => 48,  201 => 47,  195 => 45,  192 => 44,  186 => 42,  184 => 41,  178 => 39,  174 => 38,  169 => 37,  165 => 36,  161 => 34,  157 => 32,  152 => 31,  144 => 30,  142 => 29,  139 => 28,  135 => 26,  130 => 25,  122 => 24,  120 => 23,  117 => 22,  112 => 19,  98 => 18,  94 => 16,  89 => 15,  73 => 14,  70 => 13,  53 => 12,  49 => 10,  47 => 9,  42 => 7,  35 => 6,  33 => 5,  30 => 4,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "items_list/model/table/body.twig", "/mff/xcart/skins/admin/items_list/model/table/body.twig");
    }
}
