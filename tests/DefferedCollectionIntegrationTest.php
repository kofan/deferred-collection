<?php

declare(strict_types = 1);

namespace DeferredCollection;

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
        $collection = new DeferredCollection(['a' => 97, 'b' => 10, 'c' => 99, 'd' => 100]);

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
}
