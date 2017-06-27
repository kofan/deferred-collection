<?php

declare(strict_types = 1);

namespace DeferredCollection;

use ArrayObject;
use Closure;
use DeferredCollection\TestUtils\DelegatingProcessor;
use EmptyIterator;
use Generator;
use LogicException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

class DeferredCollectionTest extends MockeryTestCase
{
    private const RANGE_1_TO_5 = [1, 2, 3, 4, 5];
    private const ALPHA_NUMBERS_A_TO_D = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
    private const DELEGATE_METHOD = '_delegate_';

    /**
     * @beforeClass
     */
    public static function registerDelegatingProcessor(): void
    {
        DeferredCollectionMethodRegistry::register(self::DELEGATE_METHOD, DelegatingProcessor::class);
    }

    /**
     * @afterClass
     */
    public static function unregisterDelegatingProcessors(): void
    {
        DeferredCollectionMethodRegistry::unregister(self::DELEGATE_METHOD);
    }

    public function testCanCreateEmpty(): void
    {
        $collection = new DeferredCollection();
        $this->assertNull($collection->getIterationSubject());
    }

    public function testCanSetIterableDataAfterCreation(): void
    {
        $collection = new DeferredCollection();
        $collection->setIterationSubject(self::RANGE_1_TO_5);

        $this->assertSame(self::RANGE_1_TO_5, $collection->getIterationSubject());
    }

    public function testCanCreateFromArray(): void
    {
        $collection = new DeferredCollection(self::RANGE_1_TO_5);

        $this->assertSame(self::RANGE_1_TO_5, $collection->getIterationSubject());
        $this->assertSame(self::RANGE_1_TO_5, $collection->toArray());
    }

    public function testCanCreateFromIterable(): void
    {
        $iterable = $this->createGeneratorFromArray(self::RANGE_1_TO_5);
        $collection = new DeferredCollection($iterable);

        $this->assertSame($iterable, $collection->getIterationSubject());
        $this->assertSame(self::RANGE_1_TO_5, $collection->toArray());
    }

    private function createGeneratorFromArray(array $array): Generator
    {
        foreach (self::RANGE_1_TO_5 as $key => $value) {
            yield $value;
        }
    }

    public function testCanCreateFromCallable(): void
    {
        $executable = function () {
            return new ArrayObject(self::ALPHA_NUMBERS_A_TO_D);
        };
        $collection = new DeferredCollection($executable);

        $this->assertInstanceOf(Closure::class, $executable);
        $this->assertSame((array) self::ALPHA_NUMBERS_A_TO_D, $collection->toArray());
    }

    public function testProcessorsAreNotExecutedUntilIteration(): void
    {
        $x2 = Mockery::spy(function ($iterable) {
            foreach ($iterable as $value) {
                yield 2 * $value;
            }
        });

        $collection = new DeferredCollection(self::RANGE_1_TO_5);
        $collection->{self::DELEGATE_METHOD}($x2);

        $x2->shouldNotHaveReceived('__invoke');
        $array = $collection->toArray();
        $x2->shouldHaveReceived('__invoke');

        $this->assertSame([2, 4, 6, 8, 10], $array);
    }

    public function testProcessorsAreExecutedInTheRightOrder(): void
    {
        /** @var DeferredCollection $collection */
        $collection = (new DeferredCollection(self::RANGE_1_TO_5))
            ->{self::DELEGATE_METHOD}(function ($iterable) {
                foreach ($iterable as $value) {
                    yield 2 * $value;
                }
            })
            ->{self::DELEGATE_METHOD}(function ($iterable) {
                foreach ($iterable as $value) {
                    yield 1 + $value;
                }
            });

        $this->assertSame([
            1 + 2 * 1,
            1 + 2 * 2,
            1 + 2 * 3,
            1 + 2 * 4,
            1 + 2 * 5,
        ], $collection->toArray());
    }

    public function testCanBeTraversedWithForeach(): void
    {
        $collection = new DeferredCollection($this->createGeneratorFromArray(self::RANGE_1_TO_5));
        foreach ($collection as $i => $value) {
            $this->assertSame(self::RANGE_1_TO_5[$i], $value);
        }
    }

    public function testGetTheFirstGeneratorValue(): void
    {
        $collection = new DeferredCollection($this->createGeneratorFromArray(self::RANGE_1_TO_5));
        $this->assertSame(1, $collection->getValue());
    }

    public function testCannotBeModifedAfterExecution(): void
    {
        $collection = new DeferredCollection(self::RANGE_1_TO_5);
        $collection->toArray();

        $this->expectException(LogicException::class);
        $collection->{self::DELEGATE_METHOD}('noop');
    }

    public function testCanGetSingleValueOfTheProcessingResult(): void
    {
        $collection = new DeferredCollection(self::RANGE_1_TO_5);
        $collection->{self::DELEGATE_METHOD}(
            function ($iterable) {
                return [array_sum($iterable)];
            },
            $isSingleValue = true
        );

        $sum = $collection->getValue();
        $this->assertSame($sum, array_sum(self::RANGE_1_TO_5));

        $array = $collection->toArray();
        $this->assertSame([$sum], $array);
    }

    public function testCanGetSingleDefaultValueWhenIterableIsEmpty(): void
    {
        $defaultValue = 100;

        $collection = new DeferredCollection([]);
        $this->assertSame($defaultValue, $collection->getValue($defaultValue));

        $collection = new DeferredCollection(new EmptyIterator());
        $this->assertSame($defaultValue, $collection->getValue($defaultValue));
    }

    public function testCanConvertMultiValueToJson(): void
    {
        $collection = new DeferredCollection(self::RANGE_1_TO_5);
        $collection->{self::DELEGATE_METHOD}(function ($iterable) {
            return array_reverse($iterable);
        });

        $jsonData = $collection->jsonSerialize();
        $jsonString = $collection->toJson();

        $this->assertSame([5, 4, 3, 2, 1], $jsonData);
        $this->assertSame('[5,4,3,2,1]', $jsonString);
    }

    public function testCanConvertSingleValueToJson(): void
    {
        $collection = new DeferredCollection(self::RANGE_1_TO_5);
        $collection->{self::DELEGATE_METHOD}(
            function ($iterable) {
                return [max(...$iterable)];
            },
            $isSingleValue = true
        );

        $jsonData = $collection->jsonSerialize();
        $jsonString = $collection->toJson();

        $this->assertSame(5, $jsonData);
        $this->assertSame('5', $jsonString);
    }

    public function testCannotCallUnexistingMethod(): void
    {
        $this->expectException(RuntimeException::class);
        (new DeferredCollection([]))->{'SOME_UNEXISTING_METHOD'}();
    }

    public function testCannotIterateOverEmptyCollection(): void
    {
        $this->expectException(RuntimeException::class);
        (new DeferredCollection())->toArray();
    }
}
