<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use ArrayObject;
use DeferredCollection\TestUtils\DummyModel;
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

    public function testInstantiatesModelsWithListIndexing(): void
    {
        $index = 0;
        $instantiateProcessor = new InstantiateProcessor(DummyModel::class);

        foreach ($instantiateProcessor->process(self::MODELS_WITH_ID_AND_NAME) as $key => $model) {
            $this->assertInstanceOf(DummyModel::class, $model);
            $this->assertSame(self::MODELS_WITH_ID_AND_NAME[$index], $model->getArrayCopy());
            $this->assertSame($index, $key);
            ++$index;
        }
        $this->assertSame($index, count(self::MODELS_WITH_ID_AND_NAME));
    }

    public function testInstantiatesModelsWithFieldValueIndexing(): void
    {
        $index = 0;
        $instantiateProcessor = new InstantiateProcessor(DummyModel::class, 'id');

        foreach ($instantiateProcessor->process(self::MODELS_WITH_ID_AND_NAME) as $key => $model) {
            $this->assertInstanceOf(DummyModel::class, $model);
            $this->assertSame(self::MODELS_WITH_ID_AND_NAME[$index], $model->getArrayCopy());
            $this->assertSame($model->id, $key);
            ++$index;
        }
        $this->assertSame($index, count(self::MODELS_WITH_ID_AND_NAME));
    }

    public function testInstantiatesModelsWithMethodValueIndexing(): void
    {
        $index = 0;
        $instantiateProcessor = new InstantiateProcessor(DummyModel::class, 'getId');

        foreach ($instantiateProcessor->process(self::MODELS_WITH_ID_AND_NAME) as $key => $model) {
            $this->assertInstanceOf(DummyModel::class, $model);
            $this->assertSame(self::MODELS_WITH_ID_AND_NAME[$index], $model->getArrayCopy());
            $this->assertSame($model->getId(), $key);
            ++$index;
        }
        $this->assertSame($index, count(self::MODELS_WITH_ID_AND_NAME));
    }
}
