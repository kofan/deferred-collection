<?php

declare(strict_types = 1);

namespace DeferredCollection\Processor;

use PHPUnit\Framework\TestCase;

class MapProcessorTest extends TestCase
{
    private const RANGE_0_TO_5 = [0, 1, 2, 3, 4, 5];

    public function testShouldBeMultiValue(): void
    {
        $this->assertFalse(
            (new MapProcessor('noop'))->isSingleValue()
        );
    }

    public function testMapsTheValues()
    {
        $mapProcessor = new MapProcessor(function ($value) {
            return 10 * $value;
        });

        $index = 0;
        foreach ($mapProcessor->process(self::RANGE_0_TO_5) as $key => $value) {
            $this->assertSame($index, $key);
            $this->assertSame(10 * self::RANGE_0_TO_5[$index], $value);
            ++$index;
        }
    }

    public function testMapsTheValuesAndKeys(): void
    {
        $mapProcessor = new MapProcessor(
            function ($value, $key) {
                return [5 * $key, 10 * $value];
            },
            $mapKeys = true
        );

        $index = 0;
        foreach ($mapProcessor->process(self::RANGE_0_TO_5) as $key => $value) {
            $this->assertSame(5 * $index, $key);
            $this->assertSame(10 * self::RANGE_0_TO_5[$index], $value);
            ++$index;
        }
    }
}
