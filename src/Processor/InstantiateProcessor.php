<?php

declare(strict_types = 1);

namespace DeferredCollection\Processor;

use RuntimeException;

class InstantiateProcessor extends AbstractMultiValueProcessor
{
    /** @var string */
    private $modelClassName;

    /** @var string */
    private $indexBy;

    /** @var bool */
    private $isMethod;

    /**
     * @param string $modelClassName
     * @param string $indexBy
     */
    public function __construct(string $modelClassName, string $indexBy = '')
    {
        if (!class_exists($modelClassName)) {
            throw new RuntimeException($modelClassName . ' should be a valid class name');
        }

        $this->modelClassName = $modelClassName;
        $this->indexBy = $indexBy;
        $this->isMethod = $indexBy && method_exists($this->modelClassName, $indexBy);
    }

    /**
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        $className = $this->modelClassName;
        $indexBy = $this->indexBy;

        foreach ($iterable as $key => $value) {
            $model = new $className($value);
            $index = $key;

            if ($indexBy) {
                $index = $this->isMethod ? $model->$indexBy() : $model->$indexBy;
            }

            yield $index => $model;
        }
    }
}
