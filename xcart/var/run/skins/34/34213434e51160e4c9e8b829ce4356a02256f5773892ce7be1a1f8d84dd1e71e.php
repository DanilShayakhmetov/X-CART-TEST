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

/* /home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/product/details/parts/page.tabs.twig */
class __TwigTemplate_0c00b92ff0dac5fd4e87eda273c9ea57b6f08d8e92a0b00ca185f21b351f88dc extends \XLite\Core\Templating\Twig\Template
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
        if ($this->getAttribute(($context["this"] ?? null), "getTabs", [], "method")) {
            // line 8
            echo "  <div class=\"product-details-tabs\">

    <div class=\"tabs\">
      <ul class=\"tabs primary\">
        ";
            // line 12
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getTabs", [], "method"));
            foreach ($context['_seq'] as $context["index"] => $context["tab"]) {
                // line 13
                echo "          <li class=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute(($context["this"] ?? null), "getTabClass", [0 => $context["tab"]], "method"), "html", null, true);
                echo "\">
            <a data-id=\"";
                // line 14
                echo $this->getAttribute($context["tab"], "id", []);
                echo "\"
               ";
                // line 15
                if ($this->getAttribute($context["tab"], "alt_id", [])) {
                    echo "data-alt-id=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["tab"], "alt_id", []), "html", null, true);
                    echo "\"";
                }
                // line 16
                echo "               href=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('url')->getCallable(), [$this->env, $context, "product", "", ["product_id" => $this->getAttribute($this->getAttribute(($context["this"] ?? null), "product", []), "productId", [])]]), "html", null, true);
                echo "#";
                echo $this->getAttribute($context["tab"], "id", []);
                echo "\"
               data-toggle=\"tab\">";
                // line 17
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["tab"], "name", []), "html", null, true);
                echo "</a>
          </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['index'], $context['tab'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 20
            echo "      </ul>
    </div>

    <div class=\"tabs-container\">
      ";
            // line 24
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["this"] ?? null), "getTabs", [], "method"));
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
            foreach ($context['_seq'] as $context["_key"] => $context["tab"]) {
                // line 25
                echo "        ";
                if ($this->getAttribute($context["tab"], "alt_id", [])) {
                    // line 26
                    echo "          <div id=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["tab"], "alt_id", []), "html", null, true);
                    echo "\"></div>
        ";
                }
                // line 28
                echo "        <div id=\"";
                echo $this->getAttribute($context["tab"], "id", []);
                echo "\" class=\"tab-container hacky-container\">
          <a name=\"";
                // line 29
                echo $this->getAttribute($context["tab"], "id", []);
                echo "\"></a>
          ";
                // line 30
                if ($this->getAttribute($context["tab"], "template", [])) {
                    // line 31
                    echo "            ";
                    $fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath($this->getAttribute($context["tab"], "template", []));                    list($templateWrapperText, $templateWrapperStart) = $this->getThis()->startMarker($fullPath);
                    if ($templateWrapperText) {
echo $templateWrapperStart;
}

                    $this->loadTemplate($this->getAttribute($context["tab"], "template", []), "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/product/details/parts/page.tabs.twig", 31)->display($context);
                    if ($templateWrapperText) {
                        echo $this->getThis()->endMarker($fullPath, $templateWrapperText);
                    }
                    // line 32
                    echo "
          ";
                } else {
                    // line 34
                    echo "            ";
                    if ($this->getAttribute($context["tab"], "widget", [])) {
                        // line 35
                        echo "              ";
                        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => $this->getAttribute($context["tab"], "widget", []), "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
                        echo "

            ";
                    } else {
                        // line 38
                        echo "              ";
                        if ($this->getAttribute($context["tab"], "list", [])) {
                            // line 39
                            echo "                ";
                            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget_list')->getCallable(), [$this->env, $context, [0 => $this->getAttribute($context["tab"], "list", []), "product" => $this->getAttribute(($context["this"] ?? null), "product", [])]]), "html", null, true);
                            echo "
              ";
                        } else {
                            // line 41
                            echo "                ";
                            if ($this->getAttribute($context["tab"], "widgetObject", [])) {
                                // line 42
                                echo "                  ";
                                echo $this->getAttribute($this->getAttribute($context["tab"], "widgetObject", []), "display", [], "method");
                                echo "
                ";
                            }
                            // line 44
                            echo "              ";
                        }
                        // line 45
                        echo "            ";
                    }
                    // line 46
                    echo "          ";
                }
                // line 47
                echo "        </div>
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
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tab'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 49
            echo "    </div>

  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/product/details/parts/page.tabs.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  186 => 49,  171 => 47,  168 => 46,  165 => 45,  162 => 44,  156 => 42,  153 => 41,  147 => 39,  144 => 38,  137 => 35,  134 => 34,  130 => 32,  119 => 31,  117 => 30,  113 => 29,  108 => 28,  102 => 26,  99 => 25,  82 => 24,  76 => 20,  67 => 17,  60 => 16,  54 => 15,  50 => 14,  45 => 13,  41 => 12,  35 => 8,  33 => 7,  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/home/ruslan/Projects/next/output/xcart/src/skins/crisp_white/customer/product/details/parts/page.tabs.twig", "");
    }
}
