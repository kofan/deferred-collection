<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

abstract class AbstractSingleValueProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSingleValue(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        return [$this->computeSingleValue($iterable)];
    }

    /**
     * @param iterable $iterable
     *
     * @return mixed
     */
    abstract protected function computeSingleValue(iterable $iterable);
}
