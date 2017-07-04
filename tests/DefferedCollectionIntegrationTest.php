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
            ->map(function ($value, $key) {
                return [$value, $key];
            }, $mapKeys = true)
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
        $IDs = [101, 102, 103];
        $collection = new DeferredCollection($IDs);

        $collection
            ->map(function ($value) {
                return ['id' => $value];
            })
            ->instantiate(DummyModel::class);

        /** @var DummyModel[] $array */
        $array = $collection->toArray();

        $this->assertSame($IDs[0], $array[0]->getId());
        $this->assertSame($IDs[1], $array[1]->getId());
        $this->assertSame($IDs[2], $array[2]->getId());
    }
}
