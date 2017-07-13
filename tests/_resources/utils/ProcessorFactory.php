<?php

declare(strict_types=1);

namespace DeferredCollection\TestUtils;

use DeferredCollection\Processor\ProcessorInterface;

abstract class ProcessorFactory
{
    /**
     * @return string
     */
    public static function createProcessorMethodName(): string
    {
        return uniqid('processor_');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) - $iterable
     */
    public static function createDummyProcessorClass(): string
    {
        $classObject = new class() implements ProcessorInterface {
            public function isSingleValue(): bool
            {
                return false;
            }

            public function process(iterable $iterable): iterable
            {
                return [];
            }
        };

        return get_class($classObject);
    }
}
