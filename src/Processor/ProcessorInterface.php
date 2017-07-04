<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

interface ProcessorInterface
{
    /**
     * Whether this processor results in a single value (for example, reduce) instead of the list of values.
     *
     * @return bool
     */
    public function isSingleValue(): bool;

    /**
     * Process the iterable and returns the modified iterable data.
     * Even if there is a single value result (for example, reduce) it will be still represented as an iterable with a single item.
     *
     * @param iterable $iterable
     *
     * @return iterable
     */
    public function process(iterable $iterable): iterable;
}
