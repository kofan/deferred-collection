<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use PHPUnit\Framework\TestCase;

class ReduceProcessorTest extends TestCase
{
    private const NUMS_1_3_5 = [1, 3, 5];

    public function testShouldBeSingleValue(): void
    {
        $this->assertTrue(
            (new ReduceProcessor('noop'))->isSingleValue()
        );
    }

    public function testReducesTheValues(): void
    {
        $reduceProcessor = new ReduceProcessor(
            function ($result, $value, $key) {
                return $result + $value + $key;
            },
            $initialValue = 10
        );

        $expectedValue = 10 + (1 + 0) + (3 + 1) + (5 + 2);
        $this->assertSame([$expectedValue], $reduceProcessor->process(self::NUMS_1_3_5));
    }
}
