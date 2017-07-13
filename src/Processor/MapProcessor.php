<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

class MapProcessor extends AbstractMultiValueProcessor
{
    /** @var callable */
    private $callback;

    /**
     * @param callable $callback Mapping callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
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
        $mapped = ($this->callback)($value, $key);

        if ($this->shouldMapKeys()) {
            [$mappedKey, $mappedValue] = $mapped;
        } else {
            [$mappedKey, $mappedValue] = [$key, $mapped];
        }

        return [$mappedKey, $mappedValue];
    }

    /**
     * @return bool
     */
    protected function shouldMapKeys(): bool
    {
        return false;
    }
}
