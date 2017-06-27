<?php

declare(strict_types = 1);

namespace DeferredCollection\Processor;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class InstantiateProcessorTest extends TestCase
{
    private const MODELS_WITH_ID_AND_NAME = [
        ['id' => 1, 'name' => 'Name 1'],
        ['id' => 2, 'name' => 'Name 2'],
    ];

    public function testShouldBeMultiValue(): void
    {
        $this->assertFalse(
            (new InstantiateProcessor(ArrayObject::class))->isSingleValue()
        );
    }

    public function testThrowsAnExceptionWhenInvalidClassName(): void
    {
        $this->expectException(RuntimeException::class);
        (new InstantiateProcessor('noop'));
    }

    public function testInstantiatesTheValuesWithIntegerIndexing(): void
    {
        $modelClassName = ArrayObject::class;
        $instantiateProcessor = new InstantiateProcessor($modelClassName);
        $index = 0;

        foreach ($instantiateProcessor->process(self::MODELS_WITH_ID_AND_NAME) as $key => $model) {
            $this->assertInstanceOf($modelClassName, $model);
            $this->assertSame(self::MODELS_WITH_ID_AND_NAME[$index], $model->getArrayCopy());
            $this->assertSame($index, $key);
            ++$index;
        }
        $this->assertSame($index, count(self::MODELS_WITH_ID_AND_NAME));
    }

    public function testInstantiatesTheValuesWithFieldIndexing(): void
    {
        $modelClassName = get_class(new class() extends ArrayObject {
            public function __construct($input = [], $flags = 0, $iterator_class = 'ArrayIterator')
            {
                parent::__construct($input, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS, $iterator_class);
            }
        });
        $instantiateProcessor = new InstantiateProcessor($modelClassName, 'id');
        $index = 0;

        foreach ($instantiateProcessor->process(self::MODELS_WITH_ID_AND_NAME) as $key => $model) {
            $this->assertInstanceOf($modelClassName, $model);
            $this->assertSame(self::MODELS_WITH_ID_AND_NAME[$index], $model->getArrayCopy());
            $this->assertSame($model->id, $key);
            ++$index;
        }
        $this->assertSame($index, count(self::MODELS_WITH_ID_AND_NAME));
    }

    public function testInstantiatesTheValuesWithMethodIndexing(): void
    {
        $modelClassName = get_class(new class() extends ArrayObject {
            public function getId()
            {
                return $this['id'];
            }
        });
        $instantiateProcessor = new InstantiateProcessor($modelClassName, 'getId');
        $index = 0;

        foreach ($instantiateProcessor->process(self::MODELS_WITH_ID_AND_NAME) as $key => $model) {
            $this->assertInstanceOf($modelClassName, $model);
            $this->assertSame(self::MODELS_WITH_ID_AND_NAME[$index], $model->getArrayCopy());
            $this->assertSame($model->getId(), $key);
            ++$index;
        }
        $this->assertSame($index, count(self::MODELS_WITH_ID_AND_NAME));
    }
}
