<?php

declare(strict_types=1);

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
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        foreach ($iterable as $key => $value) {
            if (($this->predicate)($value, $key)) {
                yield $key => $value;
            }
        }
    }
}
