<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Types\Scalar;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class WordType extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     * @throws Error
     * @throws InvariantViolation
     */
    public function serialize($value)
    {
        if (!$this->isWord($value)) {
            throw new InvariantViolation('Could not serialize following value as Word: ' . Utils::printSafe($value));
        }

        return $this->parseValue($value);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     *
     * @return mixed
     * @throws Error
     */
    public function parseValue($value)
    {
        if (!$this->isWord($value)) {
            throw new Error('Cannot represent following value as Word: ' . Utils::printSafeJson($value));
        }

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * @param Node       $valueNode
     * @param array|null $variables
     *
     * @return mixed
     * @throws Error
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }
        if (!$this->isWord($valueNode->value)) {
            throw new Error('Not a valid Word', [$valueNode]);
        }

        return $valueNode->value;
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private function isWord($value)
    {
        return preg_match('/^[a-z0-9_\.]+$/i', $value);
    }
}
