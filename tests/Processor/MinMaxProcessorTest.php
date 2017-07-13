<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MinMaxProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name 2'],
        ['id' => 3, 'name' => 'Name 3'],
    ];

    private const NUMBERS_LIST = [2, 1, 5, 6, 7, 0, 3, 4];

    public function testInvalidIterateeParameterType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MinProcessor(100);
    }

    public function testWithRegularValue(): void
    {
        $actualMinValue = (new MinProcessor())->process(self::NUMBERS_LIST);
        $actualMaxValue = (new MaxProcessor())->process(self::NUMBERS_LIST);

        $expectedMinValue = min(self::NUMBERS_LIST);
        $expectedMaxValue = max(self::NUMBERS_LIST);

        $this->assertSame([$expectedMinValue], $actualMinValue);
        $this->assertSame([$expectedMaxValue], $actualMaxValue);
    }

    public function testWithPropertyValue(): void
    {
        $models = self::MODELS_WITH_ID_AND_NAME;

        $actualMinValue = (new MinProcessor('[id]'))->process($models);
        $actualMaxValue = (new MaxProcessor('[id]'))->process($models);

        $expectedMinValue = $models[0];
        $expectedMaxValue = $models[count($models) - 1];

        $this->assertSame([$expectedMinValue], $actualMinValue);
        $this->assertSame([$expectedMaxValue], $actualMaxValue);
    }

    public function testWithCallbackValue(): void
    {
        $models = self::MODELS_WITH_ID_AND_NAME;
        $callback = function ($item) {
            return $item['id'];
        };

        $actualMinValue = (new MinProcessor($callback))->process($models);
        $actualMaxValue = (new MaxProcessor($callback))->process($models);

        $expectedMinValue = $models[0];
        $expectedMaxValue = $models[count($models) - 1];

        $this->assertSame([$expectedMinValue], $actualMinValue);
        $this->assertSame([$expectedMaxValue], $actualMaxValue);
    }
}
