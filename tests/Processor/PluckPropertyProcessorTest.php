<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use ArrayObject;
use PHPUnit\Framework\TestCase;

class PluckPropertyProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 0, 'name' => 'Name 0'],
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name 2'],
    ];

    public function testMatchPropertyWithArrayItemValue(): void
    {
        $originalItems = $this->getModelRawArrays();
        $iterable = (new PluckPropertyProcessor('[name]'))
            ->process($originalItems);

        $matchedItems = iterator_to_array($iterable);
        $expectedItems = array_map(function ($item) {
            return $item['name'];
        }, $originalItems);

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
        $iterable = (new PluckPropertyProcessor('name'))
            ->process($originalObjects);

        $matchedObjects = iterator_to_array($iterable);
        $expectedObjects = array_map(function ($object) {
            return $object->name;
        }, $originalObjects);

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
}
