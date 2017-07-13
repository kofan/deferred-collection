<?php

declare(strict_types=1);

namespace DeferredCollection\TestUtils;

use ArrayObject;

class DummyModel extends ArrayObject
{
    /**
     * @param array $input
     */
    public function __construct(array $input = [])
    {
        parent::__construct($input, self::STD_PROP_LIST | self::ARRAY_AS_PROPS);
    }

    /**
     * return int|null.
     */
    public function getId(): ?int
    {
        return $this['id'] ?? null;
    }
}
