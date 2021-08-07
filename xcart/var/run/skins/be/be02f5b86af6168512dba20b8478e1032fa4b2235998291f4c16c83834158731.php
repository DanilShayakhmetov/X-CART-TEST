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

/* /mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/button/apple_pay_hide_or_labels.twig */
class __TwigTemplate_b23fbe1fd86fe85fb997b8fd76851555e5a3f038565a7984d950285773028d10 extends \XLite\Core\Templating\Twig\Template
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
    function hideTheOrLabelsIfApplePayDisabled() {
        if (!window.ApplePaySession || !ApplePaySession.canMakePayments()) {
            // Hide \"OR\" immediately
            var btns = document.querySelectorAll('.apple-pay-button-container');
            for (i = 0; i < btns.length; ++i) {
                if (btns[i].previousElementSibling) {
                    btns[i].previousElementSibling.style.display = 'none';
                }
            }
        }
    }
    hideTheOrLabelsIfApplePayDisabled();
</script>
";
    }

    public function getTemplateName()
    {
        return "/mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/button/apple_pay_hide_or_labels.twig";
    }

    public function getDebugInfo()
    {
        return array (  30 => 6,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "/mff/xcart/skins/customer/modules/XPay/XPaymentsCloud/button/apple_pay_hide_or_labels.twig", "");
    }
}
