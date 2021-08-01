<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';
    \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);

    changeNotificationGreetings();
    changeAdminNotificationTranslations();
    changeCustomerNotificationTranslations();

    \XLite\Core\Database::getEM()->flush();
};

function changeNotificationGreetings()
{
    $result = false;

    $greetingsToChange = [
        'emailNotificationCustomerGreeting' => [
            'old' => '<h3>Hey %recipient_name%</h3>',
            'new' => '<h3>Hey %recipient_name%,</h3>',
        ],
        'emailNotificationAdminGreeting' => [
            'old' => '<h3>Hey</h3>',
            'new' => '<h3>Hey there,</h3>',
        ],
    ];

    foreach ($greetingsToChange as $label => $value) {
        $languageLabel = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')
            ->findOneBy(['name' => $label]);

        if (
            $languageLabel
            && $languageLabel->getTranslation('en')
            && $languageLabel->getTranslation('en')->getLabel() === $value['old']
        ) {
            $languageLabel->getTranslation('en')->setLabel($value['new']);
            $result = true;
        }
    }

    return $result;
}

function changeAdminNotificationTranslations()
{
    $notificationsToChange = [
        'failed_transaction' => [
            'old' => '<p dir="ltr">One of your customers was about to make a purchase, but your payment processor failed to charge his card for some reason.&nbsp;</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">Wonder why it may have happened? It&rsquo;s likely that your customer entered a wrong card number, password or CVC, had no money on his card, closed his browser by mistake or just fleeted away.</p><p dir="ltr">It may be a good idea to contact him and offer your help.</p>',
            'new' => '<p dir="ltr">Hmmm . . . let&rsquo;s solve this one together:</p><p dir="ltr">One of your customers was trying to make a purchase, but the payment was declined..</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">This can happen for lots of reasons: your customer entered a wrong card number, password, or CVC; maxed out the credit limit, failed to activate the card initially, closed the browser by mistake . . .</p><p dir="ltr">Whatever the cause and the case, it may be a good idea to contact the customer and offer your help.</p>',
            'greetingEnabled' => false,
            'description' => 'This notification is sent to the administrator if payment on the checkout failed',
        ],
        'low_limit_warning' => [
            'old' => '<h3 dir="ltr">Heads-up, </h3><p dir="ltr">I see that some of your products are about to run out of stock.&nbsp;</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">According to our stats you&rsquo;ll sell the last item by %latest_sale_date%. So it&rsquo;s high time to replenish them. You are not going to sell fresh air, are you?</p>',
            'new' => '<h3 dir="ltr">Heads-up!</h3><p dir="ltr">You&rsquo;re about to stock out! According to our stats, you&rsquo;ll likely sell the last item of this product by %latest_sale_date%. Since you can&rsquo;t sell what you don&rsquo;t have, consider replenishing the product ASAP.</p><p dir="ltr">%dynamic_message%</p>',
        ],
        'order_changed' => [
            'old' => '<p dir="ltr">I&rsquo;ve saved all your order changes just in case you might need them in future. Please, take a look and make sure that everything is ok.</p>',
            'new' => '<p dir="ltr">Here are the order changes just in case you need to refer to them in the future. Please review the order history and make sure that everything looks correct.</p>',
            'description' => 'This notification is sent to both the administrator and customer when the status of an order is updated - in the event that no other type of notification is sent.',
        ],
        'order_canceled' => [
            'old' => '<p dir="ltr">I&rsquo;m sorry to see that you had to cancel the order. Hope your customer will change his mind and end up buying from you soon. I&rsquo;ll keep my fingers crossed.</p>',
            'new' => '<p dir="ltr">Just a note to confirm that this order has been canceled. Please make any adjustments necessary.</p>',
            'description' => 'This notification is sent to both the administrator and customer when an order gets the status Canceled.',
        ],
        'order_created' => [
            'old' => '<p dir="ltr">There’s a new order for you. Go ahead, show your customer you care and process the order ASAP.</p>',
            'new' => '<p dir="ltr">Commerce is happening! There’s a new order for you. Go ahead and process it ASAP so your customer enjoys quick service and delivery.</p>',
            'subject' => 'You’ve got a new order! #%order_number% for %order_total%',
            'description' => 'This notification is sent to both the administrator and customer when a new order is created from scratch and gets Awaiting Payment or Authorized statuses.',
        ],
        'order_failed' => [
            'old' => '<p dir="ltr">I see that your order was declined by %payment_method_name%. It may be a good idea to contact your customer and offer your help.</p>',
            'new' => '<p dir="ltr">It looks like an order payment was declined by %payment_method_name%. Help the customer (and save the sale) by contacting your customer and offering your help.</p>',
            'description' => 'This notification is sent to both the administrator and customer when an order gets the status Declined.',
        ],
        'order_processed' => [
            'old' => '<p dir="ltr">Your order has been processed. Way to go!</p>',
            'new' => '<h3 dir="ltr">Great news!</h3><p dir="ltr">This order has been processed and paid for. Keep on selling!</p>',
            'greetingEnabled' => false,
            'description' => 'This notification is sent to both the administrator and customer when an order gets the status Paid.',
        ],
        'profile_created' => [
            'old' => '<p>One more customer has just signed up: %customer_email%. Looks like your business is growing :)</p>',
            'new' => '<h3 dir="ltr">Hello and congratulations!</h3><p dir="ltr">One more customer has just created a profile on your online store: %customer_email%. Looks like your business is growing :)</p>',
            'greetingEnabled' => false,
            'subject' => "Great news: You've got a new customer!",
            'description' => 'This notification is sent to both the administrator and customer when a new customer profile is created.',
        ],
        'profile_deleted' => [
            'old' => '<p dir="ltr">One of your customers has just silently deleted his profile: %deleted_profile%</p><p dir="ltr">Make sure you no longer keep his personal data anywhere in your database</p>',
            'new' => '<p dir="ltr">One of your customers has just deleted his or her profile: %deleted_profile%</p><p dir="ltr">Be cool and respect privacy guidelines by making sure that you no longer keep account-related personal data anywhere in your database.</p>',
            'subject' => 'Bummer, one customer fewer',
        ],
        'recover_password_request' => [
            'old' => '<p dir="ltr">I&rsquo;ve received a request to reset your forgotten password.</p><p dir="ltr">If you didn&rsquo;t make this request, good, there&rsquo;s nothing else you need to do. Just safely ignore this message.&nbsp;</p><p>Otherwise, please <a href="%recover_url%">click here to change your password</a> right away.</p>',
            'new' => '<p dir="ltr">I&rsquo;ve received a request to reset your password.</p><p dir="ltr">If you didn&rsquo;t make this request, simply do . . . nothing. It&rsquo;s all good.</p><p dir="ltr">Otherwise, please proceed and <a href="%recover_url%">click here to change your password</a>.</p>',
        ],
        'failed_admin_login' => [
            'old' => '<h3 dir="ltr">Hey there,</h3><p dir="ltr">Someone is trying to break into your admin account.&nbsp;</p><p dir="ltr">Is that you? Ok, then. You can reset the forgotten password <a href="%reset_link%">here</a>.</p><p dir="ltr">If you didn&rsquo;t mean to reset your password, then you can safely ignore this message. Your password will not change. Just make sure it&rsquo;s secure enough. &nbsp;</p>',
            'new' => '<h3 dir="ltr">Hey there,</h3><p dir="ltr">Someone (hopefully you) is trying to log into your account. If it is indeed you, cool, but it looks like you forgot your password. If so, you can retrieve and reset it <a href="%reset_link%">here</a>.</p><p dir="ltr">If these login attempts have not been made by you, you should reset your password immediately using a strong password. Also, to stay safe, make sure you don&rsquo;t share your login creds with anyone.</p>',
            'subject' => 'Password issue detected',
            'description' => 'The notification is sent to the email address of the admin user for whose account failed login attempts have been detected several times in a row.',
        ],
    ];

    foreach ($notificationsToChange as $id => $data) {
        /** @var \XLite\Model\Notification $notification */
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getTranslation('en')) {
            if (isset($data['description'])) {
                $notification->getTranslation('en')->setDescription($data['description']);
            }

            if ($notification->getTranslation('en')->getAdminText() === $data['old']) {
                $notification->getTranslation('en')->setAdminText($data['new']);

                if (isset($data['greetingEnabled'])) {
                    $notification->setAdminGreetingEnabled($data['greetingEnabled']);
                }

                if (isset($data['subject'])) {
                    $notification->getTranslation('en')->setAdminSubject($data['subject']);
                }
            }
        }
    }

    return true;
}

function changeCustomerNotificationTranslations()
{
    $notificationsToChange = [
        'order_changed' => [
            'old' => '<p dir="ltr">As previously agreed, we&rsquo;ve changed your order. Here is a new invoice for you, fresh and updated. Check it out to make sure that nothing is missing.</p>',
            'new' => '<p dir="ltr">As promised, here&rsquo;s an update to your order. Here&rsquo;s the updated status as reflected on your invoice. Please check it to make sure that everything looks good.</p>',
            'subject' => 'Order #%order_number% is updated',
        ],
        'order_canceled' => [
            'old' => '<h3 dir="ltr">Hi %recipient_name%,</h3><p dir="ltr">We had to cancel your order. If that was you who initiated the cancellation, please tell us where we failed and let us at least try to make up for our faults. For any questions and feedback, just hit &#39;Reply&#39;.</p>',
            'new' => '<p dir="ltr">Your order has been canceled. If you initiated the cancellation, please tell us why so we can continually improve our selection and service. To let us know, or for any questions, just hit reply.</p>',
            'greetingEnabled' => true,
        ],
        'order_created' => [
            'old' => '<p dir="ltr">Thank you for shopping with us!&nbsp;</p><p dir="ltr">This is just a quick note to let you know that we&rsquo;ve received your order and we will process it as soon as we can. We will keep you updated on the status of your order.&nbsp;</p>',
            'new' => '<p dir="ltr">Thank you for shopping with us!</p><p dir="ltr">This is just a quick note to let you know that we&rsquo;ve received your order and it&rsquo;s in process!. We&rsquo;ll keep you updated on the status of your order.</p>',
        ],
        'order_failed' => [
            'old' => '<h3 dir="ltr">Hi %recipient_name%,</h3><p dir="ltr">Something went wrong and %payment_method_name% failed to charge your credit card.&nbsp;</p><p dir="ltr">If everything seems alright on your side and you are eager to proceed with the purchase, please reply back. We&rsquo;ll try to find out what&rsquo;s up.</p>',
            'new' => '<h3 dir="ltr">Hi %recipient_name%,</h3><p dir="ltr">We&rsquo;ve hit a snag. %payment_method_name% declined your payment. Perhaps your credit card is expired or you need to update some other details.</p><p dir="ltr">If everything seems alright on your side and you are eager to proceed with the purchase, please reply. We&rsquo;ll try to find out what&rsquo;s up and rectify the issue.</p>',
        ],
        'order_processed' => [
            'old' => '<h2 dir="ltr">%recipient_name%, your order has been paid successfully.</h2><h3 dir="ltr">And we are just as excited as you are</h3><p dir="ltr"><br>Take a look below for all the confirmation details you&rsquo;ll need.</p>',
            'new' => '<h2 dir="ltr" style="text-align: center;">Your order was paid successfully, %recipient_name%!</h2><h3 dir="ltr" style="text-align: center;">And we&rsquo;re just as excited as you are!</h3><p dir="ltr">Payment for your order has been received and processed. We&rsquo;ll let you know when the order ships or is ready for pickup.</p>',
        ],
        'order_shipped' => [
            'old' => '<h2 dir="ltr">Bingo %recipient_name%!</h2><h3 dir="ltr">Your item is on its way to your doorstep</h3><p dir="ltr"><br>We&rsquo;ve packed your order and sent it off on a journey to your place. The delivery shouldn&rsquo;t take much time as we turbo-charged our van, so it gets to you extra fast.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">If you have any questions feel free to get in touch. We are always here and super happy to help you.</p>',
            'new' => '<h2 dir="ltr" style="text-align: center;">Bingo, %recipient_name%!</h2><h3 dir="ltr" style="text-align: center;">Your item is on its way to your doorstep!</h3><p dir="ltr"><br>Woohoo! Your order is en route to you and should be delivered to you soon!</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">If you have any questions, feel free to get in touch. We&rsquo;re always here and happy to help you.</p>',
        ],
        'order_tracking_information' => [
            'old' => '<p dir="ltr">We&rsquo;ve sent your parcel off on a journey to your porch via %shipping_method_name%.&nbsp;</p><p dir="ltr">Below you&rsquo;ll find shipping details and a handy tracking link you can use to follow your order&rsquo;s trip to your door.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">Don&#39;t worry if the order tracking doesn&#39;t work right away. It may take some time to have this information available.</p>',
            'new' => '<p dir="ltr">Your order is en route via %shipping_method_name%.&nbsp;</p><p dir="ltr">Below you&rsquo;ll find shipping details and a handy tracking link you can use to follow your order&rsquo;s trip to your door.</p><p dir="ltr">%dynamic_message%</p><p dir="ltr">Don&#39;t worry if order tracking isn&rsquo;t available right away. It may take some time for the tracking number to enter the system.</p>',
        ],
        'order_waiting_for_approve' => [
            'old' => '<h3 dir="ltr">Hi %recipient_name%,</h3><p dir="ltr">Thank you for shopping on %company_name%!&nbsp;</p><p dir="ltr">Our financial team needs some time to manually review your order.</p><p dir="ltr">Please wait for a couple of hours and take another look at your inbox. There should be one more email ready and waiting.</p>',
            'new' => '<p dir="ltr">Thank you for shopping on %company_name%!&nbsp;</p><p dir="ltr">Your order is currently pending and we&rsquo;ll let you know when it reaches the next phase. In the meantime, you don&rsquo;t need to do anything. Just keep an eye on your inbox.</p>',
            'subject' => 'Order #%order_number% is pending',
            'greetingEnabled' => true,
        ],
        'profile_created' => [
            'old' => '<h3 dir="ltr">Welcome to %company_name%!</h3><p dir="ltr">The world of online shopping is now at your fingertips</p><p dir="ltr">We&rsquo;ve created an account for you. There you will find:</p><ul><li dir="ltr">all your previous (yes, we&rsquo;ve saved them all!) and future orders;</li><li dir="ltr">addresses in case you need to get things delivered to different places;</li><li dir="ltr">profile details, where you can edit your login and password.</li><li dir="ltr">messages you&rsquo;ve ever sent or received from us;</li></ul><p>Sign in to <a href="%sign_in_url%">your account</a> to start using all these features. And if you have any questions, just reply back.&nbsp;</p>',
            'new' => '<h3 dir="ltr" style="text-align: center;">Welcome to %company_name%!</h3><p dir="ltr">Your account is active and ready! You&rsquo;re ready to shop and manage your profile. In addition to finding all of the products you need, you&rsquo;ll also be able to access:</p><ul><li dir="ltr">your order history</li><li dir="ltr">your address book so you can ship to multiple locations</li><li dir="ltr">security features where you can edit your login and password</li><li dir="ltr">communications between you and our helpful employees</li></ul><p>Sign in to <a href="%sign_in_url%">your account</a> to start using all these features. And if you have any questions, just reply.&nbsp;</p>',
        ],
        'recover_password_request' => [
            'old' => '<p dir="ltr">We&rsquo;ve received a request to reset your forgotten password.</p><p dir="ltr">If you didn&rsquo;t ask for it, good, there&rsquo;s nothing else you need to do. Just safely ignore this message.&nbsp;</p><p>Otherwise, please <a href="%recover_url%">click here to change your password</a> right away.</p>',
            'new' => '<p dir="ltr">We&rsquo;ve received a request to reset your forgotten password.</p><p dir="ltr">If you didn&rsquo;t make the request (or you remembered the password on your own), cool, there&rsquo;s nothing else you need to do. Just safely ignore this message.&nbsp;</p><p>Otherwise, please <a href="%recover_url%">click here to change your password</a> right away.</p>',
        ],
        'register_anonymous' => [
            'old' => '<h2 dir="ltr">Thank you for shopping with us, %recipient_name%!&nbsp;</h2><p dir="ltr"><br>We noticed that you&rsquo;ve made a purchase using our guest checkout. It looks like you are too pressed for time to create an account in our store. No problem. We did it for you:</p><p dir="ltr">Email: <strong>%customer_email%</strong><br>Password: <strong>%customer_password%</strong></p><p dir="ltr">There you will find all your previous (yes, we&rsquo;ve saved them all!) and future orders, addresses, messages, and profile details, where you can change your password at any time.</p><p dir="ltr">If you need any help, just reply back.&nbsp;</p>',
            'new' => '<h2 dir="ltr" style="text-align: center;">Thank you for shopping with us, %recipient_name%!&nbsp;</h2><p dir="ltr">We noticed that you&rsquo;ve made a purchase using our guest checkout. To save you time on future orders, we&rsquo;ve created an account for you. Simply log in with the following credentials.</p><p dir="ltr">Email: <strong>%customer_email%</strong><br>Password: <strong>%customer_password%</strong></p><p dir="ltr">There you&rsquo;ill find all of your orders, addresses, messages, and profile details.&nbsp;</p><p dir="ltr">Enjoy expedited checkout and don&rsquo;t forget to change your password for extra security.</p><p dir="ltr">If you need any help, just reply.&nbsp;</p>',
        ],
    ];

    foreach ($notificationsToChange as $id => $data) {
        /** @var \XLite\Model\Notification $notification */
        $notification = \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id);

        if ($notification && $notification->getTranslation('en')) {
            if (isset($data['description'])) {
                $notification->getTranslation('en')->setDescription($data['description']);
            }

            if ($notification->getTranslation('en')->getCustomerText() === $data['old']) {
                $notification->getTranslation('en')->setCustomerText($data['new']);

                if (isset($data['greetingEnabled'])) {
                    $notification->setCustomerGreetingEnabled($data['greetingEnabled']);
                }

                if (isset($data['subject'])) {
                    $notification->getTranslation('en')->setCustomerSubject($data['subject']);
                }
            }
        }
    }

    return true;
}