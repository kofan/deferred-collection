<?php

namespace DeferredCollection\Processor;

class ReduceProcessor extends AbstractSingleValueProcessor
{
    /** @var callable */
    private $callback;

    /** @var callable */
    private $initialValue;

    /**
     * @param callable $callback Reducing callback
     * @param mixed|null $initialValue
     */
    public function __construct(callable $callback, $initialValue = null)
    {
        $this->callback = $callback;
        $this->initialValue = $initialValue;
    }

    /**
     * {@inheritDoc}
     */
    public function process(iterable $iterable) : iterable
    {
        $resultValue = $this->initialValue;

        foreach ($iterable as $key => $value) {
            $resultValue = ($this->callback)($resultValue, $value, $key);
        }

        return [$resultValue];
    }
}
