<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

class MaxProcessor extends AbstractBoundaryValueProcessor
{
    /**
     * {@inheritdoc}
     */
    protected function isNotNewBoundary($currentValue, $newValue): bool
    {
        return $newValue <= $currentValue;
    }
}
