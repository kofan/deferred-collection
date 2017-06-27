<?php

declare(strict_types = 1);

namespace DeferredCollection\TestUtils;

use ArrayObject;

class DummyModel extends ArrayObject
{
    public function __construct(array $input = [], int $flags = 0, string $iteratorClass = 'ArrayIterator')
    {
        parent::__construct($input, self::STD_PROP_LIST | self::ARRAY_AS_PROPS, $iteratorClass);
    }

    /**
     * return int|null.
     */
    public function getId(): ?int
    {
        return $this['id'] ?? null;
    }
}
