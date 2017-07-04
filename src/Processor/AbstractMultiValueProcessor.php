<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

abstract class AbstractMultiValueProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSingleValue(): bool
    {
        return false;
    }
}
