<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use ArrayObject;
use PHPUnit\Framework\TestCase;

class MatchPropertyProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 0, 'name' => 'Name 0'],
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name X'],
        ['id' => 3, 'name' => 'Name X'],
    ];

    private const NAME_TO_MATCH = 'Name X';

    public function testMatchPropertyWithArrayItemValue(): void
    {
        $originalItems = $this->getModelRawArrays();
        $iterable = (new MatchPropertyProcessor('[name]', self::NAME_TO_MATCH))
            ->process($originalItems);

        $matchedItems = iterator_to_array($iterable);
        $expectedItems = array_filter($originalItems, function ($item) {
            return $item['name'] === self::NAME_TO_MATCH;
        });

        $this->assertSame($expectedItems, $matchedItems);
    }

    /**
     * @return array[]
     */
    private function getModelRawArrays(): array
    {
        return self::MODELS_WITH_ID_AND_NAME;
    }

    public function testMatchPropertyWithObjectPropertyValue(): void
    {
        $originalObjects = $this->getModelObjects();
        $iterable = (new MatchPropertyProcessor('name', self::NAME_TO_MATCH))
            ->process($originalObjects);

        $matchedObjects = iterator_to_array($iterable);
        $expectedObjects = array_filter($originalObjects, function ($object) {
            return $object->name === self::NAME_TO_MATCH;
        });

        $this->assertSame($expectedObjects, $matchedObjects);
    }

    /**
     * @return ArrayObject[]
     */
    private function getModelObjects(): array
    {
        return array_map(function (array $item) {
            return new ArrayObject($item, ArrayObject::ARRAY_AS_PROPS);
        }, $this->getModelRawArrays());
    }

    public function testMatchPropertyWithCallback(): void
    {
        $callback = function ($name) {
            return $name === self::NAME_TO_MATCH;
        };
        $originalObjects = $this->getModelObjects();
        $iterable = (new MatchPropertyProcessor('name', $callback))
            ->process($originalObjects);

        $matchedObjects = iterator_to_array($iterable);
        $expectedObjects = array_filter($originalObjects, function ($object) {
            return $object->name === self::NAME_TO_MATCH;
        });

        $this->assertSame($expectedObjects, $matchedObjects);
    }
}
