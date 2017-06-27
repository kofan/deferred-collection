<?php

namespace DeferredCollection\Processor;

abstract class AbstractMultiValueProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function isSingleValue() : bool
    {
        return false;
    }
}
