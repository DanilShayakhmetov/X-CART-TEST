<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Output;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Silex\Application;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Resolver\LanguageDataResolver;
use XCart\Bus\Query\Types\AObjectType;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class AlertType extends AObjectType
{
    /**
     * @var LanguageDataResolver
     */
    private $languageDataResolver;

    /**
     * @var array
     */
    private $languageMessages;

    /**
     * @param Application          $app
     * @param LanguageDataResolver $languageDataResolver
     */
    public function __construct(
        Application $app,
        LanguageDataResolver $languageDataResolver
    ) {
        parent::__construct($app);

        $this->languageDataResolver = $languageDataResolver;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public static function prepareParams(array $params)
    {
        return json_encode(array_values($params));
    }

    /**
     * @return array
     */
    protected function defineConfig()
    {
        return [
            'fields' => [
                'type'    => Type::string(), // @todo: num (info, success, warning, danger)
                'message' => Type::string(),
                'params'  => Type::string(),
                'translated' => [
                    'type'    => Type::string(),
                    'resolve' => function ($value, $args, Context $context, ResolveInfo $info) {
                        $messages = $this->getLanguageMessages();

                        return LanguageDataResolver::getMessageWithReplacedParams(
                            $messages[$value['message']],
                            $value['params'] ? json_decode($value['params'], true) : []
                        );
                    },
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    private function getLanguageMessages(): array
    {
        if (!isset($this->languageMessages)) {
            $messages = $this->languageDataResolver->getLanguageMessages(null, [], null, new ResolveInfo([]));
            $this->languageMessages = [];
            foreach ((array) $messages as $label) {
                $this->languageMessages[$label['name']] = $label['label'];
            }
        }

        return $this->languageMessages;
    }
}
