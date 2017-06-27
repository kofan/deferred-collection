<?php

namespace DeferredCollection\TestUtils;

use DeferredCollection\Processor\ProcessorInterface;

abstract class ProcessorFactory
{
    public static function createProcessorMethodName() : string
    {
        return uniqid('processor_');
    }

    public static function createDummyProcessorClass() : string
    {
        $classObject = new class implements ProcessorInterface
        {
            public function isSingleValue() : bool
            {
                return false;
            }
            public function process(iterable $iterable) : iterable
            {
                return [];
            }
        };
        return get_class($classObject);
    }
}
