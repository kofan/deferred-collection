<?php

declare(strict_types=1);

namespace DeferredCollection;

use DeferredCollection\Processor\FilterProcessor;
use DeferredCollection\Processor\InstantiateProcessor;
use DeferredCollection\Processor\MapProcessor;
use DeferredCollection\Processor\MatchPropertyProcessor;
use DeferredCollection\Processor\MaxProcessor;
use DeferredCollection\Processor\MinProcessor;
use DeferredCollection\Processor\PluckPropertyProcessor;
use DeferredCollection\Processor\ProcessorInterface;
use DeferredCollection\Processor\ReduceProcessor;
use InvalidArgumentException;

/**
 * @static
 */
abstract class DeferredCollectionMethodRegistry
{
    /**
     * @var array Key -> collection method name
     *            Value -> processor class name
     */
    private static $methodNameToProcessorClass = [
        'map' => MapProcessor::class,
        'filter' => FilterProcessor::class,
        'reduce' => ReduceProcessor::class,
        'instantiate' => InstantiateProcessor::class,
        'matchProperty' => MatchPropertyProcessor::class,
        'pluckProperty' => PluckPropertyProcessor::class,
        'min' => MinProcessor::class,
        'max' => MaxProcessor::class,
    ];

    /**
     * @param string $processorName
     * @param string $processorClass
     *
     * @throws InvalidArgumentException
     */
    public static function register(string $processorName, string $processorClass): void
    {
        if (!is_subclass_of($processorClass, ProcessorInterface::class)) {
            throw new InvalidArgumentException('Processor class name should implement ' . ProcessorInterface::class . ' interface');
        }
        static::$methodNameToProcessorClass[$processorName] = $processorClass;
    }

    /**
     * @param string $processorName
     *
     * @return bool
     */
    public static function unregister(string $processorName): bool
    {
        if (isset(static::$methodNameToProcessorClass[$processorName])) {
            unset(static::$methodNameToProcessorClass[$processorName]);

            return true;
        }

        return false;
    }

    /**
     * @param string $processorName
     *
     * @return string
     */
    public static function findProcessorClass(string $processorName): string
    {
        return static::$methodNameToProcessorClass[$processorName]
            ?? '';
    }
}
