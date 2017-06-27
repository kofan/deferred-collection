<?php

namespace DeferredCollection\Processor;

abstract class AbstractSingleValueProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function isSingleValue() : bool
    {
        return true;
    }
}
