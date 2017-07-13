<?php

declare(strict_types=1);

namespace DeferredCollection;

use DeferredCollection\TestUtils\DummyModel;
use PHPUnit\Framework\TestCase;

class DefferedCollectionIntegrationTest extends TestCase
{
    public function testCollectionWithMapAndFilter(): void
    {
        $collection = new DeferredCollection(function () {
            for ($i = 0; $i <= 5; ++$i) {
                yield $i;
            }
        });

        $collection
            ->map(function ($value) {
                return $value * 3;
            })
            ->filter(function ($value) {
                return $value % 2 === 1;
            });

        $this->assertSame([1 => 3, 3 => 9, 5 => 15], $collection->toArray());
    }

    public function testCollectionWithMapAndFilterAndReduce(): void
    {
        $letterOrds = ['a' => 97, 'b' => 10, 'c' => 99, 'd' => 100];
        $collection = new DeferredCollection($letterOrds);

        $collection
            ->mapWithKeys(function ($value, $key) {
                return [$value, $key];
            })
            ->filter(function ($value, $key) {
                return ord($value) === $key;
            })
            ->reduce(function ($result, $value) {
                return $result . $value;
            }, '');

        $this->assertSame('acd', $collection->getValue());
    }

    public function testCollectionWithMapAndInstantiate(): void
    {
        $ids = [101, 102, 103];
        $collection = new DeferredCollection($ids);

        $collection
            ->map(function ($value) {
                return ['id' => $value];
            })
            ->instantiate(DummyModel::class);

        /** @var DummyModel[] $array */
        $array = $collection->toArray();

        $this->assertSame($ids[0], $array[0]->getId());
        $this->assertSame($ids[1], $array[1]->getId());
        $this->assertSame($ids[2], $array[2]->getId());
    }

    public function testCollectionWithPluckPropertyAndMin(): void
    {
        $models = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 0],
        ];
        $collection = new DeferredCollection($models);

        $collection
            ->pluckProperty('[id]')
            ->min();

        $this->assertSame(0, $collection->getValue());
    }

    public function testCollectionWithMatchPropertyAndMax(): void
    {
        $models = [
            ['id' => 1, 'name' => 'X'],
            ['id' => 2, 'name' => 'Y'],
            ['id' => 0, 'name' => 'X'],
        ];
        $collection = new DeferredCollection($models);

        $collection
            ->matchProperty('[name]', 'X')
            ->max('[id]');

        $this->assertSame($models[0], $collection->getValue());
    }
}
