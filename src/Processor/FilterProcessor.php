<?php

namespace DeferredCollection\Processor;

class FilterProcessor extends AbstractMultiValueProcessor
{
    /** @var callable */
    private $predicate;

    /**
     * @param callable $predicate Filtering predicate
     */
    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * {@inheritDoc}
     */
    public function process(iterable $iterable) : iterable
    {
        foreach ($iterable as $key => $value) {
            if (($this->predicate)($value, $key)) {
                yield $key => $value;
            }
        }
    }
}
