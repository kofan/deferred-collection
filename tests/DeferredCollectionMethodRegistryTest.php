<?php

declare(strict_types = 1);

namespace DeferredCollection;

use DeferredCollection\TestUtils\ProcessorFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DeferredCollectionMethodRegistryTest extends TestCase
{
    public function testCanRegisterNewProcessor(): void
    {
        $processorClassName = ProcessorFactory::createDummyProcessorClass();
        $processorMethodName = ProcessorFactory::createProcessorMethodName();

        DeferredCollectionMethodRegistry::register($processorMethodName, $processorClassName);
        $foundClassName = DeferredCollectionMethodRegistry::findProcessorClass($processorMethodName);

        $this->assertSame($processorClassName, $foundClassName);
    }

    public function testCanUnregisterExistingProcessor(): void
    {
        $processorClassName = ProcessorFactory::createDummyProcessorClass();
        $processorMethodName = ProcessorFactory::createProcessorMethodName();

        DeferredCollectionMethodRegistry::register($processorMethodName, $processorClassName);
        $unregisterResult = DeferredCollectionMethodRegistry::unregister($processorMethodName);

        $this->assertTrue($unregisterResult);
    }

    public function testCanAttemptToUnregisterUnexistingProcessor(): void
    {
        $unexistingProcessorName = '[[unexisting_processor]]';
        $unregisterResult = DeferredCollectionMethodRegistry::unregister($unexistingProcessorName);
        $this->assertFalse($unregisterResult);
    }

    public function testCannotRegisterProcessorWhichDoesNotImplementInterface(): void
    {
        $anonymousObject = new class() {
            /* this class does not implements ProcessorInterface */
        };
        $processorClassName = get_class($anonymousObject);
        $processorMethodName = ProcessorFactory::createProcessorMethodName();

        $this->expectException(InvalidArgumentException::class);
        DeferredCollectionMethodRegistry::register($processorMethodName, $processorClassName);
    }
}
