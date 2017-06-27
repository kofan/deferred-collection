<?php

declare(strict_types = 1);

namespace DeferredCollection\TestUtils;

use DeferredCollection\Processor\ProcessorInterface;

class DelegatingProcessor implements ProcessorInterface
{
    private $callback;
    private $isSingleValue;

    public function __construct($callback, bool $isSingleValue = false)
    {
        if (!is_callable($callback) && is_object($callback)) {
            $callback = [$callback, '__invoke'];
        }

        $this->callback = $callback;
        $this->isSingleValue = $isSingleValue;
    }

    public function isSingleValue(): bool
    {
        return $this->isSingleValue;
    }

    public function process(iterable $iterable): iterable
    {
        return ($this->callback)($iterable);
    }
}
