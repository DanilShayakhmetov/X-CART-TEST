<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception\Rebuild;

use Exception;
use GuzzleHttp\Exception\ParseException;
use XCart\Bus\Exception\RebuildException;

class AbortException extends RebuildException
{
    /**
     * @param string $transitionId
     * @param string $message
     *
     * @return RebuildException
     */
    public static function fromDownloadStepWrongResponse($transitionId, $message): RebuildException
    {
        return (new self('Package download error'))
            ->setData([
                sprintf('Entity %s, %s', $transitionId, $message),
            ]);
    }

    /**
     * @param string $transitionId
     *
     * @return RebuildException
     */
    public static function fromDownloadStepEmptyResponse($transitionId): RebuildException
    {
        return (new self('Package download error'))
            ->setData([
                sprintf('Entity %s, empty pack file', $transitionId),
            ]);
    }

    /**
     * @param string $transitionId
     * @param string $path
     *
     * @return RebuildException
     */
    public static function fromUnpackStepExtractionError($transitionId, $path): RebuildException
    {
        return (new self('Package extraction error'))
            ->setData([
                sprintf('Entry %s, cannot extract (%s)', $transitionId, $path),
            ]);
    }

    /**
     * @param string $transitionId
     * @param string $path
     *
     * @return RebuildException
     */
    public static function fromUnpackStepMissingPackage($transitionId, $path): RebuildException
    {
        return (new self('Package extraction error'))
            ->setData([
                sprintf('Entry %s, file (%s) not found', $transitionId, $path),
            ]);
    }

    /**
     * @param string $transitionId
     *
     * @return RebuildException
     */
    public static function fromCheckFSStepInvalidResponse($transitionId): RebuildException
    {
        return (new self('Hash checking error'))
            ->setData([
                sprintf('Entry %s, invalid response', $transitionId),
            ]);
    }

    /**
     * @param string $transitionId
     * @param string $message
     *
     * @return RebuildException
     */
    public static function fromCheckFSStepWrongResponse($transitionId, $message): RebuildException
    {
        return (new self('Hash checking error'))
            ->setData([
                sprintf('Entry %s, %s', $transitionId, $message),
            ]);
    }

    /**
     * @param string $path
     *
     * @return RebuildException
     */
    public static function fromCheckFSStepWrongHashFile($path): RebuildException
    {
        return (new self('Hash checking error'))
            ->setData([
                sprintf('Cannot read hashes (%s)', $path),
            ]);
    }

    /**
     * @param string[] $errors
     *
     * @return RebuildException
     */
    public static function fromUpdateModulesListStepUpdateError($errors): RebuildException
    {
        return (new self('Failed to update modules list'))
            ->setData($errors);
    }

    /**
     * @param ParseException $exception
     *
     * @return RebuildException
     */
    public static function fromUpdateModulesListStepWrongResponse(ParseException $exception): RebuildException
    {
        return (new self(
            'Failed to update modules list',
            $exception->getCode(),
            $exception
        ))
            ->setDescription(self::getExceptionDescription($exception));
    }

    /**
     * @param ParseException $exception
     *
     * @return RebuildException
     */
    public static function fromXCartStepWrongResponseFormat(ParseException $exception): RebuildException
    {
        return (new self(
            'Error thrown from X-Cart',
            $exception->getCode(),
            $exception
        ))
            ->setDescription(self::getExceptionDescription($exception));
    }

    /**
     * @param Exception $exception
     *
     * @return RebuildException
     */
    public static function fromXCartStepWrongResponse(Exception $exception): RebuildException
    {
        return (new self(
            'Error thrown from X-Cart',
            $exception->getCode(),
            $exception
        ))
            ->setDescription(self::getExceptionDescription($exception));
    }

    /**
     * @return RebuildException
     */
    public static function fromXCartStepEmptyResponse(): RebuildException
    {
        return new self('Empty response from X-Cart');
    }

    /**
     * @param string $name
     * @param array  $error
     *
     * @return RebuildException
     */
    public static function fromXCartStepErrorResponse($name, $error): RebuildException
    {
        return (new self('X-Cart rebuild step failed'))
            ->setDescription("Step {$name} failed with the following errors:")
            ->setData($error);
    }

    /**
     * @param string         $file
     * @param ParseException $previous
     *
     * @return RebuildException
     */
    public static function fromHookStepWrongResponseFormat($file, ParseException $previous): RebuildException
    {
        return (new self('X-Cart failed to execute the hook', $previous->getCode(), $previous))
            ->addData("File: {$file}")
            ->addData($previous->getMessage())
            ->addData(self::getExceptionContent($previous));
    }

    /**
     * @param string    $file
     * @param Exception $previous
     *
     * @return RebuildException
     */
    public static function fromHookStepWrongResponse($file, Exception $previous): RebuildException
    {
        return (new self('X-Cart failed to execute the hook', $previous->getCode(), $previous))
            ->addData("File: {$file}")
            ->addData($previous->getMessage());
    }

    /**
     * @return RebuildException
     */
    public static function fromHookStepEmptyResponse(): RebuildException
    {
        return (new self('Empty response from X-Cart'));
    }

    /**
     * @param string $file
     * @param mixed  $errors
     *
     * @return RebuildException
     */
    public static function fromHookStepErrorResponse($file, $errors): RebuildException
    {
        $exception = (new self('X-Cart failed to execute the hook'))
            ->addData(sprintf('X-Cart failed to execute the hook: %s', $file));

        foreach ($errors as $error) {
            $exception->addData($error);
        }

        return $exception;
    }

    /**
     * @param Exception $previous
     *
     * @return RebuildException
     */
    public static function fromRemoveModulesStepError(Exception $previous): RebuildException
    {
        return (new self('Remove module failed', $previous->getCode(), $previous))
            ->addData($previous->getMessage());
    }

    /**
     * @param string $moduleId
     *
     * @return RebuildException
     */
    public static function fromUpdateDataSourceStepMissingMarketplaceModule($moduleId): RebuildException
    {
        return (new self('Module ' . $moduleId . ' was not found in marketplace data source'));
    }

    /**
     * @param string $moduleId
     *
     * @return RebuildException
     */
    public static function fromUpdateDataSourceStepMissingModule($moduleId): RebuildException
    {
        return (new self('Module ' . $moduleId . ' was not found in data source'));
    }

    /**
     * @param string         $moduleId
     * @param ParseException $previous
     *
     * @return RebuildException
     */
    public static function fromUpgradeActionStepWrongResponseFormat($moduleId, ParseException $previous): RebuildException
    {
        return (new self('X-Cart failed to execute the upgrade action ' . $moduleId, $previous->getCode(), $previous))
            ->addData("Module: {$moduleId}")
            ->addData($previous->getMessage())
            ->addData(self::getExceptionContent($previous));
    }

    /**
     * @param string    $moduleId
     * @param Exception $previous
     *
     * @return RebuildException
     */
    public static function fromUpgradeActionStepWrongResponse($moduleId, Exception $previous): RebuildException
    {
        return (new self('X-Cart failed to execute the upgrade action ' . $moduleId, $previous->getCode(), $previous))
            ->addData("Module: {$moduleId}")
            ->addData($previous->getMessage());
    }

    /**
     * @return RebuildException
     */
    public static function fromUpgradeActionStepEmptyResponse(): RebuildException
    {
        return (new self('Empty response from X-Cart'));
    }

    /**
     * @param string $moduleId
     * @param mixed  $errors
     *
     * @return RebuildException
     */
    public static function fromUpgradeActionStepErrorResponse($moduleId, $errors): RebuildException
    {
        return (new self('X-Cart failed to execute the upgrade action ' . $moduleId))
            ->addData($errors);
    }

    /**
     * @param Exception|ParseException $exception
     *
     * @return string
     */
    private static function getExceptionDescription($exception): string
    {
        $description = $exception->getMessage();
        if ($exception->getResponse()) {
            $description .= 'Content: ' .
                $exception->getResponse()->getBody();
        }

        return $description;
    }

    /**
     * @param ParseException $exception
     *
     * @return string
     */
    private static function getExceptionContent($exception): string
    {
        return $exception->getResponse()
            ? "Content: {$exception->getResponse()->getBody()}"
            : '';
    }
}
