<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MinProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name 2'],
        ['id' => 3, 'name' => 'Name 3'],
    ];

    private const NUMBERS_LIST = [2, 1, 5, 6, 7, 0, 3, 4];

    public function testMinRegularValue(): void
    {
        $actualResult = (new MinProcessor())->process(self::NUMBERS_LIST);
        $expectedMinValue = min(self::NUMBERS_LIST);

        $this->assertSame([$expectedMinValue], $actualResult);
    }

    public function testMinPropertyValue(): void
    {
        $models = self::MODELS_WITH_ID_AND_NAME;
        $actualResult = (new MinProcessor('[id]'))->process($models);
        $expectedMinValue = $models[0];

        $this->assertSame([$expectedMinValue], $actualResult);
    }

    public function testMinCallbackValue(): void
    {
        $models = self::MODELS_WITH_ID_AND_NAME;
        $callback = function ($item) {
            return $item['id'];
        };
        $actualResult = (new MinProcessor($callback))->process($models);
        $expectedMinValue = $models[0];

        $this->assertSame([$expectedMinValue], $actualResult);
    }

    public function testInvalidMinIterateeParameterType()
    {
        $this->expectException(InvalidArgumentException::class);
        new MinProcessor(100);
    }
}
