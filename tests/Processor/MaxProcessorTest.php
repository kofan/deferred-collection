<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MaxProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name 2'],
        ['id' => 3, 'name' => 'Name 3'],
    ];

    private const NUMBERS_LIST = [2, 1, 5, 6, 7, 0, 3, 4];

    public function testMaxRegularValue(): void
    {
        $actualResult = (new MaxProcessor())->process(self::NUMBERS_LIST);
        $expectedMaxValue = max(self::NUMBERS_LIST);

        $this->assertSame([$expectedMaxValue], $actualResult);
    }

    public function testMaxPropertyValue(): void
    {
        $models = self::MODELS_WITH_ID_AND_NAME;
        $actualResult = (new MaxProcessor('[id]'))->process($models);
        $expectedMaxValue = $models[count($models) - 1];

        $this->assertSame([$expectedMaxValue], $actualResult);
    }

    public function testMaxCallbackValue(): void
    {
        $models = self::MODELS_WITH_ID_AND_NAME;
        $callback = function ($item) {
            return $item['id'];
        };
        $actualResult = (new MaxProcessor($callback))->process($models);
        $expectedMaxValue = $models[count($models) - 1];

        $this->assertSame([$expectedMaxValue], $actualResult);
    }

    public function testInvalidMaxIterateeParameterType()
    {
        $this->expectException(InvalidArgumentException::class);
        new MaxProcessor(100);
    }
}
