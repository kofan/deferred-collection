<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use InvalidArgumentException;

abstract class AbstractBoundaryValueProcessor extends AbstractSingleValueProcessor
{
    use PropertyProcessorTrait;

    /** @var string|callable|null */
    private $valueCallback;

    /**
     * @param string|callable|null $iteratee
     */
    public function __construct($iteratee = null)
    {
        if (null === $iteratee) {
            // do nothing, null is ok
        } elseif (is_string($iteratee)) {
            $this->propertyName = $iteratee;
        } elseif (is_callable($iteratee)) {
            $this->valueCallback = $iteratee;
        } else {
            throw new InvalidArgumentException('$iteratee should have either string or callable type');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function computeSingleValue(iterable $iterable)
    {
        $isFirst = true;
        $boundaryItem = $boundaryValue = null;

        foreach ($iterable as $key => $item) {
            $value = $this->extractValue($item, $key);

            if ($isFirst) {
                $isFirst = false;
            } elseif ($this->isNotNewBoundary($boundaryValue, $value)) {
                continue;
            }

            [$boundaryItem, $boundaryValue] = [$item, $value];
        }

        return $boundaryItem;
    }

    /**
     * @param mixed      $item
     * @param int|string $key
     *
     * @return mixed
     */
    private function extractValue($item, $key)
    {
        if (isset($this->propertyName)) {
            return $this->getPropertyValue($item);
        }
        if (isset($this->valueCallback)) {
            return ($this->valueCallback)($item, $key);
        }

        return $item;
    }

    /**
     * @param mixed $currentValue
     * @param mixed $newValue
     *
     * @return bool
     */
    abstract protected function isNotNewBoundary($currentValue, $newValue): bool;
}
