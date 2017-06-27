<?php

declare(strict_types = 1);

namespace DeferredCollection\Processor;

class MapProcessor extends AbstractMultiValueProcessor
{
    /** @var callable */
    private $callback;

    /** @var bool */
    private $mapKeys;

    /**
     * @param callable $callback Mapping callback
     * @param bool     $mapKeys
     */
    public function __construct(callable $callback, bool $mapKeys = false)
    {
        $this->callback = $callback;
        $this->mapKeys = $mapKeys;
    }

    /**
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        foreach ($iterable as $key => $value) {
            [$mappedKey, $mappedValue] = $this->map($key, $value);
            yield $mappedKey => $mappedValue;
        }
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return array
     */
    private function map($key, $value): array
    {
        $mappedKey = $key;
        $mappedValue = ($this->callback)($value, $key);

        if ($this->mapKeys) {
            [$mappedKey, $mappedValue] = $mappedValue;
        }

        return [$mappedKey, $mappedValue];
    }
}
