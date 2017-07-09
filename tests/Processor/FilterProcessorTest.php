<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use PHPUnit\Framework\TestCase;

class FilterProcessorTest extends TestCase
{
    private const RANGE_10_TO_15 = [10, 11, 12, 13, 14, 15];

    public function testShouldBeMultiValue(): void
    {
        $this->assertFalse(
            (new FilterProcessor('noop'))->isSingleValue()
        );
    }

    public function testFiltersTheValues(): void
    {
        $filterProcessor = new FilterProcessor(function ($value) {
            return $value % 2 === 0;
        });

        foreach ($filterProcessor->process(self::RANGE_10_TO_15) as $key => $value) {
            $this->assertTrue($value % 2 === 0);
            $this->assertSame(array_search($value, self::RANGE_10_TO_15, true), $key);
        }
    }
}
