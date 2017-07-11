<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Traversable;

class PluckPropertyProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 0, 'name' => 'Name 0'],
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name 2'],
    ];

    public function testPluckPropertyWithArrayItemValue(): void
    {
        $originalItems = $this->getModelRawArrays();

        /** @var Traversable $iterable */
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

    public function testPluckPropertyWithObjectPropertyValue(): void
    {
        $originalObjects = $this->getModelObjects();

        /** @var Traversable $iterable */
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

    public function testPluckPropertyWithScalarValuesThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/cannot access the property/');

        /** @var Traversable $iterable */
        $iterable = (new PluckPropertyProcessor('name'))->process([1, 2, 3]);
        iterator_to_array($iterable);
    }
}
