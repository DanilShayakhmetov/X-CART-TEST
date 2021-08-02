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

/* form_model/theme.twig */
class __TwigTemplate_a2890881fce1d2cfa045d16af46398b4e57c3eec4aa082eef599cb2786943a5d extends \XLite\Core\Templating\Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $_trait_0 = $this->loadTemplate("twig_form/bootstrap_3_horizontal_layout.html.twig", "form_model/theme.twig", 1);
        // line 1
        if (!$_trait_0->isTraitable()) {
            throw new RuntimeError('Template "'."twig_form/bootstrap_3_horizontal_layout.html.twig".'" cannot be used as a trait.', 1, $this->getSourceContext());
        }
        $_trait_0_blocks = $_trait_0->getBlocks();

        $this->traits = $_trait_0_blocks;

        $this->blocks = array_merge(
            $this->traits,
            [
                'form_start' => [$this, 'block_form_start'],
                'form_end' => [$this, 'block_form_end'],
                'form_row' => [$this, 'block_form_row'],
                'form_input_wrapper' => [$this, 'block_form_input_wrapper'],
                'checkbox_radio_row' => [$this, 'block_checkbox_radio_row'],
                'form_row_class' => [$this, 'block_form_row_class'],
                'form_group_class' => [$this, 'block_form_group_class'],
                'form_label' => [$this, 'block_form_label'],
                'form_label_class' => [$this, 'block_form_label_class'],
                'form_errors' => [$this, 'block_form_errors'],
                'widget_attributes' => [$this, 'block_widget_attributes'],
                'widget_validation' => [$this, 'block_widget_validation'],
                'base_field_set_row' => [$this, 'block_base_field_set_row'],
                'base_field_set_label' => [$this, 'block_base_field_set_label'],
                'base_field_set_label_attributes' => [$this, 'block_base_field_set_label_attributes'],
                'base_composite_widget' => [$this, 'block_base_composite_widget'],
                'old_widget' => [$this, 'block_old_widget'],
                'promo_widget' => [$this, 'block_promo_widget'],
                'switcher_widget' => [$this, 'block_switcher_widget'],
                'low_stock_notification_widget' => [$this, 'block_low_stock_notification_widget'],
                'datepicker_widget' => [$this, 'block_datepicker_widget'],
                'caption_widget' => [$this, 'block_caption_widget'],
                'symbol_widget' => [$this, 'block_symbol_widget'],
                'dimensions_widget' => [$this, 'block_dimensions_widget'],
                'clean_url_widget' => [$this, 'block_clean_url_widget'],
                'uploader_widget' => [$this, 'block_uploader_widget'],
            ]
        );
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 2
        echo "
";
        // line 3
        $this->displayBlock('form_start', $context, $blocks);
        // line 8
        echo "
    ";
        // line 9
        $this->displayBlock('form_end', $context, $blocks);
        // line 14
        echo "
";
        // line 15
        $this->displayBlock('form_row', $context, $blocks);
        // line 43
        echo "
";
        // line 44
        $this->displayBlock('form_input_wrapper', $context, $blocks);
        // line 54
        echo "
";
        // line 55
        $this->displayBlock('checkbox_radio_row', $context, $blocks);
        // line 69
        echo "
";
        // line 70
        $this->displayBlock('form_row_class', $context, $blocks);
        // line 73
        echo "
";
        // line 74
        $this->displayBlock('form_group_class', $context, $blocks);
        // line 77
        echo "
";
        // line 78
        $this->displayBlock('form_label', $context, $blocks);
        // line 88
        echo "
";
        // line 89
        $this->displayBlock('form_label_class', $context, $blocks);
        // line 91
        echo "
";
        // line 92
        $this->displayBlock('form_errors', $context, $blocks);
        // line 120
        $this->displayBlock('widget_attributes', $context, $blocks);
        // line 126
        $this->displayBlock('widget_validation', $context, $blocks);
        // line 136
        echo "
";
        // line 137
        $this->displayBlock('base_field_set_row', $context, $blocks);
        // line 146
        echo "
";
        // line 147
        $this->displayBlock('base_field_set_label', $context, $blocks);
        // line 164
        $this->displayBlock('base_field_set_label_attributes', $context, $blocks);
        // line 176
        echo "
";
        // line 178
        $this->displayBlock('base_composite_widget', $context, $blocks);
        // line 183
        echo "
";
        // line 185
        $this->displayBlock('old_widget', $context, $blocks);
        // line 189
        echo "
";
        // line 191
        $this->displayBlock('promo_widget', $context, $blocks);
        // line 195
        echo "
";
        // line 198
        $this->displayBlock('switcher_widget', $context, $blocks);
        // line 218
        echo "
";
        // line 221
        $this->displayBlock('low_stock_notification_widget', $context, $blocks);
        // line 226
        echo "
";
        // line 228
        echo "
";
        // line 229
        $this->displayBlock('datepicker_widget', $context, $blocks);
        // line 236
        echo "
";
        // line 238
        echo "
";
        // line 240
        echo "
";
        // line 241
        $this->displayBlock('caption_widget', $context, $blocks);
        // line 244
        echo "
";
        // line 246
        echo "
";
        // line 248
        echo "
";
        // line 249
        $this->displayBlock('symbol_widget', $context, $blocks);
        // line 264
        echo "
";
        // line 266
        echo "
";
        // line 268
        $this->displayBlock('dimensions_widget', $context, $blocks);
        // line 276
        echo "
";
        // line 278
        $this->displayBlock('clean_url_widget', $context, $blocks);
        // line 336
        echo "
";
        // line 338
        echo "
";
        // line 339
        $this->displayBlock('uploader_widget', $context, $blocks);
        // line 342
        echo "
";
    }

    // line 3
    public function block_form_start($context, array $blocks = [])
    {
        // line 4
        echo "<xlite-form-model inline-template :form=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["data_object"] ?? null), "html", null, true);
        echo "\" :original=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["view_object"] ?? null), "html", null, true);
        echo "\">";
        // line 5
        $this->displayParentBlock("form_start", $context, $blocks);
        // line 6
        echo "<validator name=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
        echo "\">";
    }

    // line 9
    public function block_form_end($context, array $blocks = [])
    {
        // line 10
        echo "  </validator>
  </form>
</xlite-form-model>
";
    }

    // line 15
    public function block_form_row($context, array $blocks = [])
    {
        // line 16
        ob_start(function () { return ''; });
        // line 17
        echo "    <div class=\"";
        $this->displayBlock("form_row_class", $context, $blocks);
        // line 18
        if ((( !($context["compound"] ?? null) || (((isset($context["force_error"]) || array_key_exists("force_error", $context))) ? (_twig_default_filter(($context["force_error"] ?? null), false)) : (false))) &&  !($context["valid"] ?? null))) {
            echo " has-error";
        }
        echo "\"
        ";
        // line 19
        if ((twig_length_filter($this->env, ($context["v_validators"] ?? null)) > 0)) {
            echo " v-bind:class=\"";
            echo "{";
            echo " 'has-error': ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validator"] ?? null), "html", null, true);
            echo ".invalid && ( ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_form"] ?? null), "html", null, true);
            echo ".submitted || ";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validator"] ?? null), "html", null, true);
            echo ".dirty ) ";
            echo "}";
            echo "\" ";
        }
        // line 20
        echo "        ";
        if ((twig_length_filter($this->env, ($context["v_show"] ?? null)) > 0)) {
            echo " v-show=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_show"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 21
        echo "    >

      ";
        // line 23
        if (($context["show_label_block"] ?? null)) {
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'label');
        }
        // line 24
        echo "
      <div class=\"input-widget ";
        // line 25
        $this->displayBlock("form_group_class", $context, $blocks);
        echo "\">
        ";
        // line 26
        $this->displayBlock("form_input_wrapper", $context, $blocks);
        echo "

        ";
        // line 28
        if ( !twig_test_empty(($context["description"] ?? null))) {
            // line 29
            echo "          <span class=\"help-block";
            // line 30
            if ((( !($context["compound"] ?? null) || (((isset($context["force_error"]) || array_key_exists("force_error", $context))) ? (_twig_default_filter(($context["force_error"] ?? null), false)) : (false))) &&  !($context["valid"] ?? null))) {
                echo " hide";
            }
            echo "\"
              ";
            // line 31
            if ((twig_length_filter($this->env, ($context["v_validators"] ?? null)) > 0)) {
                echo " v-bind:class=\"";
                echo "{";
                echo "'hide': ";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validator"] ?? null), "html", null, true);
                echo ".invalid ";
                echo "}";
                echo "\" ";
            }
            // line 32
            echo "          >
            <span class=\"text-wrapper\">";
            // line 33
            echo ($context["description"] ?? null);
            echo "</span>
          </span>
        ";
        }
        // line 37
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        // line 38
        echo "</div>

    </div>
  ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 44
    public function block_form_input_wrapper($context, array $blocks = [])
    {
        // line 45
        echo "  <div class=\"input-wrapper\">";
        // line 46
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        // line 47
        if ( !twig_test_empty(($context["help"] ?? null))) {
            // line 48
            echo "<div class=\"help-wrapper\">
        ";
            // line 49
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Tooltip", "text" => ($context["help"] ?? null)]]), "html", null, true);
            echo "
      </div>";
        }
        // line 52
        echo "  </div>
";
    }

    // line 55
    public function block_checkbox_radio_row($context, array $blocks = [])
    {
        // line 56
        ob_start(function () { return ''; });
        // line 57
        echo "    <div class=\"";
        $this->displayBlock("form_row_class", $context, $blocks);
        if ( !($context["valid"] ?? null)) {
            echo " has-error";
        }
        echo "\"
        ";
        // line 58
        if ((twig_length_filter($this->env, ($context["v_show"] ?? null)) > 0)) {
            echo " v-show=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_show"] ?? null), "html", null, true);
            echo "\" ";
        }
        // line 59
        echo "    >
      ";
        // line 60
        if (($context["show_label_block"] ?? null)) {
            // line 61
            echo "      <div class=\"";
            $this->displayBlock("form_label_class", $context, $blocks);
            echo "\"></div>";
        }
        // line 62
        echo "      <div class=\"input-widget ";
        $this->displayBlock("form_group_class", $context, $blocks);
        echo "\">
        ";
        // line 63
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        echo "
        ";
        // line 64
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        echo "
      </div>
    </div>
  ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 70
    public function block_form_row_class($context, array $blocks = [])
    {
        // line 71
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["form_row_class"] ?? null), "html", null, true);
    }

    // line 74
    public function block_form_group_class($context, array $blocks = [])
    {
        // line 75
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["input_grid"] ?? null), "html", null, true);
    }

    // line 78
    public function block_form_label($context, array $blocks = [])
    {
        // line 79
        ob_start(function () { return ''; });
        // line 80
        echo "    <div class=\"control-label\">
      ";
        // line 81
        $this->displayParentBlock("form_label", $context, $blocks);
        echo "
      ";
        // line 82
        if ( !twig_test_empty(($context["label_description"] ?? null))) {
            // line 83
            echo "        <span class=\"help-block\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["label_description"] ?? null), "html", null, true);
            echo "</span>
      ";
        }
        // line 85
        echo "    </div>
  ";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 89
    public function block_form_label_class($context, array $blocks = [])
    {
    }

    // line 92
    public function block_form_errors($context, array $blocks = [])
    {
        // line 93
        if ( !$this->getAttribute(($context["form"] ?? null), "parent", [])) {
            // line 94
            echo "    ";
            if (((twig_length_filter($this->env, ($context["errors"] ?? null)) > 0) || (twig_length_filter($this->env, ($context["v_validators"] ?? null)) > 0))) {
                // line 95
                echo "<div class=\"alert alert-danger\">
        <ul class=\"list-unstyled field-errors\">";
                // line 97
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                    // line 98
                    echo "<li v-xlite-backend-validator=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_model"] ?? null), "html", null, true);
                    echo "\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["error"], "message", []), "html", null, true);
                    echo "</li>";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 100
                echo "</ul>
      </div>
    ";
            }
            // line 103
            echo "  ";
        } else {
            // line 104
            echo "    ";
            if (((twig_length_filter($this->env, ($context["errors"] ?? null)) > 0) || (twig_length_filter($this->env, ($context["v_validators"] ?? null)) > 0))) {
                // line 105
                echo "      <div class=\"help-block\">
        <ul class=\"list-unstyled field-errors\">";
                // line 107
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                    // line 108
                    echo "<li v-xlite-backend-validator=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_model"] ?? null), "html", null, true);
                    echo "\">";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $this->getAttribute($context["error"], "message", []), "html", null, true);
                    echo "</li>";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 110
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["v_validators"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["validator"]) {
                    // line 111
                    echo "<li v-if=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["validator"], "html", null, true);
                    echo " && ( ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_form"] ?? null), "html", null, true);
                    echo ".submitted || ";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validator"] ?? null), "html", null, true);
                    echo ".dirty )\" v-text=\"";
                    echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["validator"], "html", null, true);
                    echo "\"></li>";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['validator'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 113
                echo "</ul>
      </div>
    ";
            }
            // line 116
            echo "  ";
        }
    }

    // line 120
    public function block_widget_attributes($context, array $blocks = [])
    {
        // line 121
        if ( !twig_test_empty(($context["v_model"] ?? null))) {
            echo "v-model=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_model"] ?? null), "html", null, true);
            echo "\"";
        }
        // line 122
        echo "  ";
        $this->displayBlock("widget_validation", $context, $blocks);
        // line 123
        $this->displayParentBlock("widget_attributes", $context, $blocks);
    }

    // line 126
    public function block_widget_validation($context, array $blocks = [])
    {
        // line 127
        if ( !twig_test_empty(($context["v_validate"] ?? null))) {
            // line 128
            echo "    initial=\"off\" v-validate:";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validate_path"] ?? null), "html", null, true);
            echo "=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validate"] ?? null), "html", null, true);
            echo "\"
  ";
        }
        // line 130
        echo "  ";
        if ( !twig_test_empty(($context["v_validate_trigger"] ?? null))) {
            // line 131
            echo "    v-xlite-validate-trigger=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_validate_trigger"] ?? null), "html", null, true);
            echo "\"
  ";
        }
    }

    // line 137
    public function block_base_field_set_row($context, array $blocks = [])
    {
        // line 138
        echo "  <fieldset class=\"";
        if (((((isset($context["force_error"]) || array_key_exists("force_error", $context))) ? (_twig_default_filter(($context["force_error"] ?? null), false)) : (false)) &&  !($context["valid"] ?? null))) {
            echo "has-error";
        }
        echo "\">
    ";
        // line 139
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'label');
        echo "
    <div ";
        // line 140
        $this->displayBlock("widget_container_attributes", $context, $blocks);
        echo ">
      ";
        // line 141
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        echo "
      ";
        // line 142
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'widget');
        echo "
    </div>
  </fieldset>
";
    }

    // line 147
    public function block_base_field_set_label($context, array $blocks = [])
    {
        // line 148
        echo "  <legend>
    ";
        // line 149
        if ( !twig_test_empty(($context["label"] ?? null))) {
            // line 150
            echo "      <h2 ";
            $this->displayBlock("base_field_set_label_attributes", $context, $blocks);
            echo ">";
            // line 151
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["label"] ?? null), "html", null, true);
            // line 152
            if ( !twig_test_empty(($context["help"] ?? null))) {
                // line 153
                echo "<div class=\"help-wrapper\">
            ";
                // line 154
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\Tooltip", "text" => ($context["help"] ?? null)]]), "html", null, true);
                echo "
          </div>";
            }
            // line 157
            echo "        ";
            if (($context["collapse"] ?? null)) {
                echo "<i class=\"fa fa-chevron-down\" aria-hidden=\"true\"></i>";
            }
            // line 158
            echo "      </h2>
      ";
            // line 159
            if ( !twig_test_empty(($context["description"] ?? null))) {
                echo "<span class=\"help-block\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["description"] ?? null), "html", null, true);
                echo "</span>";
            }
            // line 160
            echo "    ";
        }
        // line 161
        echo "  </legend>
";
    }

    // line 164
    public function block_base_field_set_label_attributes($context, array $blocks = [])
    {
        // line 165
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["title_attr"] ?? null));
        foreach ($context['_seq'] as $context["attrname"] => $context["attrvalue"]) {
            // line 166
            echo " ";
            // line 167
            if (($context["attrvalue"] === true)) {
                // line 168
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["attrname"], "html", null, true);
                echo "=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["attrname"], "html", null, true);
                echo "\"";
            } elseif ( !(            // line 169
$context["attrvalue"] === false)) {
                // line 170
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["attrname"], "html", null, true);
                echo "=\"";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, $context["attrvalue"], "html", null, true);
                echo "\"";
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 178
    public function block_base_composite_widget($context, array $blocks = [])
    {
        // line 179
        $context["attr"] = twig_array_merge(($context["attr"] ?? null), ["class" => twig_trim_filter(((($this->getAttribute(($context["attr"] ?? null), "class", [], "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["attr"] ?? null), "class", []), "")) : ("")) . " form-inline"))]);
        // line 180
        $this->displayBlock("form_widget_compound", $context, $blocks);
    }

    // line 185
    public function block_old_widget($context, array $blocks = [])
    {
        // line 186
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => ($context["fieldClass"] ?? null), 1 => ($context["fieldOptions"] ?? null)]]), "html", null, true);
    }

    // line 191
    public function block_promo_widget($context, array $blocks = [])
    {
        // line 192
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\SimplePromoBlock", "promoId" => ($context["promoId"] ?? null)]]), "html", null, true);
    }

    // line 198
    public function block_switcher_widget($context, array $blocks = [])
    {
        // line 199
        echo "<div class=\"onoffswitch\">
    ";
        // line 200
        if (($context["disabled"] ?? null)) {
            // line 201
            echo "      <input type=\"hidden\" name=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["full_name"] ?? null), "html", null, true);
            echo "\" value=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo "\"/>
    ";
        } else {
            // line 203
            echo "      ";
            // line 204
            echo "    ";
        }
        // line 205
        echo "    <input
        type=\"checkbox\" ";
        // line 206
        $this->displayBlock("widget_attributes", $context, $blocks);
        if ((isset($context["value"]) || array_key_exists("value", $context))) {
            echo " value=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo "\"";
        }
        if (($context["checked"] ?? null)) {
            echo " checked=\"checked\"";
        }
        echo " />
    <label for=\"";
        // line 207
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\">
      <div class=\"onoffswitch-inner\">
        <div class=\"on-caption\">";
        // line 209
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [($context["on_caption"] ?? null)]), "html", null, true);
        echo "</div>
        <div class=\"off-caption\">";
        // line 210
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), [($context["off_caption"] ?? null)]), "html", null, true);
        echo "</div>
      </div>
      <span class=\"onoffswitch-switch\"></span>
    </label>
  </div>";
    }

    // line 221
    public function block_low_stock_notification_widget($context, array $blocks = [])
    {
        // line 222
        $this->displayBlock("switcher_widget", $context, $blocks);
    }

    // line 229
    public function block_datepicker_widget($context, array $blocks = [])
    {
        // line 230
        echo "<div class=\"input-group\">
    <span class=\"input-group-addon\"><i class=\"fa fa-calendar\" aria-hidden=\"true\"></i></span>";
        // line 232
        $this->displayBlock("form_widget_simple", $context, $blocks);
        // line 233
        echo "<input type=\"hidden\" class=\"datepicker-value-input\" name=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["full_name"] ?? null), "html", null, true);
        echo "\" value=\"";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
        echo "\">
  </div>";
    }

    // line 241
    public function block_caption_widget($context, array $blocks = [])
    {
        // line 242
        echo "<div class=\"caption\">";
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["caption"] ?? null), "html", null, true);
        echo "</div>";
    }

    // line 249
    public function block_symbol_widget($context, array $blocks = [])
    {
        // line 250
        if ((($context["left_symbol"] ?? null) || ($context["right_symbol"] ?? null))) {
            // line 251
            echo "    <div class=\"input-group\">
      ";
            // line 252
            if ( !twig_test_empty(($context["left_symbol"] ?? null))) {
                // line 253
                echo "        <span class=\"input-group-addon\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["left_symbol"] ?? null), "html", null, true);
                echo "</span>
      ";
            }
            // line 255
            $this->displayBlock("form_widget_simple", $context, $blocks);
            // line 256
            if ( !twig_test_empty(($context["right_symbol"] ?? null))) {
                // line 257
                echo "        <span class=\"input-group-addon\">";
                echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["right_symbol"] ?? null), "html", null, true);
                echo "</span>
      ";
            }
            // line 259
            echo "    </div>
  ";
        } else {
            // line 261
            $this->displayBlock("form_widget_simple", $context, $blocks);
        }
    }

    // line 268
    public function block_dimensions_widget($context, array $blocks = [])
    {
        // line 269
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "length", []), 'widget');
        echo "
    <span class=\"separator\">&#215;</span>
    ";
        // line 271
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "width", []), 'widget');
        echo "
    <span class=\"separator\">&#215;</span>
    ";
        // line 273
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "height", []), 'widget');
    }

    // line 278
    public function block_clean_url_widget($context, array $blocks = [])
    {
        // line 280
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "clean_url", []), 'row');
        echo "

  ";
        // line 282
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "clean_url_ext", []), 'row');
        echo "

  ";
        // line 284
        if (($context["hasForcibleConflict"] ?? null)) {
            // line 285
            echo "    <div class=\"clean-url-conflict help-block not-padded\" v-show=\"!isCleanUrlAutogenerate()\">
      <div class=\"clean-url-force\">
        ";
            // line 287
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "force", []), 'row');
            echo "
      </div>
      <div class=\"clean-url-force\">
        ";
            // line 290
            echo ($context["errorMessage"] ?? null);
            echo "
        ";
            // line 291
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\ToolTip", "text" => ($context["resolveHint"] ?? null), "caption" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Resolve the conflict"]), "isImageTag" => "false", "className" => "help-icon"]]), "html", null, true);
            echo "
      </div>
    </div>
  ";
        } elseif (        // line 294
($context["hasUnForcibleConflict"] ?? null)) {
            // line 295
            echo "    <div class=\"clean-url-conflict help-block not-padded\" v-show=\"!isCleanUrlAutogenerate()\">
      <div class=\"clean-url-force\">
        ";
            // line 297
            echo ($context["errorMessage"] ?? null);
            echo "
        ";
            // line 298
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => "XLite\\View\\ToolTip", "text" => ($context["resolveHint"] ?? null), "caption" => call_user_func_array($this->env->getFunction('t')->getCallable(), ["Resolve the conflict"]), "isImageTag" => "false", "className" => "help-icon"]]), "html", null, true);
            echo "
      </div>
    </div>
  ";
        } elseif (        // line 301
($context["errorMessage"] ?? null)) {
            // line 302
            echo "    <div class=\"clean-url-conflict help-block not-padded\" v-show=\"!isCleanUrlAutogenerate()\">
      <div class=\"clean-url-force\">
        ";
            // line 304
            echo ($context["errorMessage"] ?? null);
            echo "
      </div>
    </div>
  ";
        }
        // line 308
        echo "
  ";
        // line 309
        if (($context["disabled"] ?? null)) {
            // line 310
            echo "    <div class=\"clean-url-disabled-info help-block not-padded\">
      ";
            // line 311
            echo ($context["disabledComment"] ?? null);
            echo "
    </div>
  ";
        }
        // line 314
        echo "
  <div class=\"clean-url-result help-block not-padded\"
       v-xlite-clean-url=\"";
        // line 316
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["v_model"] ?? null), "html", null, true);
        echo "\"
       clean-url-template=\"";
        // line 317
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["cleanUrlTemplate"] ?? null), "html", null, true);
        echo "\"
       clean-url-saved-value=\"";
        // line 318
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["savedValue"] ?? null), "html", null, true);
        echo "\"
       clean-url-extension=\"";
        // line 319
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["extension"] ?? null), "html", null, true);
        echo "\">
    <div class=\"clean-url-result-info\" v-show=\"!isCleanUrlAutogenerate()\">
      <span class=\"result-label\">";
        // line 321
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["Result"]), "html", null, true);
        echo ": </span>
      ";
        // line 322
        if (($context["disabled"] ?? null)) {
            // line 323
            echo "        <span class=\"saved\" v-show=\"!isCleanURLChanged()\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["cleanUrl"] ?? null), "html", null, true);
            echo "</span>
      ";
        } else {
            // line 325
            echo "        <a href=\"";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["cleanUrl"] ?? null), "html", null, true);
            echo "\" target=\"_blank\" class=\"saved\" v-show=\"!isCleanURLChanged()\">";
            echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, ($context["cleanUrl"] ?? null), "html", null, true);
            echo "</a>
      ";
        }
        // line 327
        echo "      <span class=\"calculated\" v-show=\"isCleanURLChanged()\">";
        echo "{{{";
        echo "getCleanURLResult()";
        echo "}}}";
        echo "</span>
    </div>
    <div class=\"clean-url-result-info\" v-else>";
        // line 329
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('t')->getCallable(), ["The clean URL will be generated automatically."]), "html", null, true);
        echo "</div>
  </div>

  ";
        // line 332
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "autogenerate", []), 'row');
    }

    // line 339
    public function block_uploader_widget($context, array $blocks = [])
    {
        // line 340
        echo XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('widget')->getCallable(), [$this->env, $context, [0 => ($context["uploaderClass"] ?? null), 1 => ($context["options"] ?? null)]]), "html", null, true);
    }

    public function getTemplateName()
    {
        return "form_model/theme.twig";
    }

    public function getDebugInfo()
    {
        return array (  979 => 340,  976 => 339,  972 => 332,  966 => 329,  958 => 327,  950 => 325,  944 => 323,  942 => 322,  938 => 321,  933 => 319,  929 => 318,  925 => 317,  921 => 316,  917 => 314,  911 => 311,  908 => 310,  906 => 309,  903 => 308,  896 => 304,  892 => 302,  890 => 301,  884 => 298,  880 => 297,  876 => 295,  874 => 294,  868 => 291,  864 => 290,  858 => 287,  854 => 285,  852 => 284,  847 => 282,  842 => 280,  839 => 278,  835 => 273,  830 => 271,  825 => 269,  822 => 268,  817 => 261,  813 => 259,  807 => 257,  805 => 256,  803 => 255,  797 => 253,  795 => 252,  792 => 251,  790 => 250,  787 => 249,  781 => 242,  778 => 241,  769 => 233,  767 => 232,  764 => 230,  761 => 229,  757 => 222,  754 => 221,  745 => 210,  741 => 209,  736 => 207,  724 => 206,  721 => 205,  718 => 204,  716 => 203,  708 => 201,  706 => 200,  703 => 199,  700 => 198,  696 => 192,  693 => 191,  689 => 186,  686 => 185,  682 => 180,  680 => 179,  677 => 178,  665 => 170,  663 => 169,  658 => 168,  656 => 167,  654 => 166,  650 => 165,  647 => 164,  642 => 161,  639 => 160,  633 => 159,  630 => 158,  625 => 157,  620 => 154,  617 => 153,  615 => 152,  613 => 151,  609 => 150,  607 => 149,  604 => 148,  601 => 147,  593 => 142,  589 => 141,  585 => 140,  581 => 139,  574 => 138,  571 => 137,  563 => 131,  560 => 130,  552 => 128,  550 => 127,  547 => 126,  543 => 123,  540 => 122,  534 => 121,  531 => 120,  526 => 116,  521 => 113,  507 => 111,  503 => 110,  493 => 108,  489 => 107,  486 => 105,  483 => 104,  480 => 103,  475 => 100,  465 => 98,  461 => 97,  458 => 95,  455 => 94,  453 => 93,  450 => 92,  445 => 89,  439 => 85,  433 => 83,  431 => 82,  427 => 81,  424 => 80,  422 => 79,  419 => 78,  415 => 75,  412 => 74,  408 => 71,  405 => 70,  396 => 64,  392 => 63,  387 => 62,  382 => 61,  380 => 60,  377 => 59,  371 => 58,  363 => 57,  361 => 56,  358 => 55,  353 => 52,  348 => 49,  345 => 48,  343 => 47,  341 => 46,  339 => 45,  336 => 44,  328 => 38,  326 => 37,  320 => 33,  317 => 32,  307 => 31,  301 => 30,  299 => 29,  297 => 28,  292 => 26,  288 => 25,  285 => 24,  281 => 23,  277 => 21,  270 => 20,  256 => 19,  250 => 18,  247 => 17,  245 => 16,  242 => 15,  235 => 10,  232 => 9,  226 => 6,  224 => 5,  218 => 4,  215 => 3,  210 => 342,  208 => 339,  205 => 338,  202 => 336,  200 => 278,  197 => 276,  195 => 268,  192 => 266,  189 => 264,  187 => 249,  184 => 248,  181 => 246,  178 => 244,  176 => 241,  173 => 240,  170 => 238,  167 => 236,  165 => 229,  162 => 228,  159 => 226,  157 => 221,  154 => 218,  152 => 198,  149 => 195,  147 => 191,  144 => 189,  142 => 185,  139 => 183,  137 => 178,  134 => 176,  132 => 164,  130 => 147,  127 => 146,  125 => 137,  122 => 136,  120 => 126,  118 => 120,  116 => 92,  113 => 91,  111 => 89,  108 => 88,  106 => 78,  103 => 77,  101 => 74,  98 => 73,  96 => 70,  93 => 69,  91 => 55,  88 => 54,  86 => 44,  83 => 43,  81 => 15,  78 => 14,  76 => 9,  73 => 8,  71 => 3,  68 => 2,  25 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "form_model/theme.twig", "/mff/xcart/skins/admin/form_model/theme.twig");
    }
}
