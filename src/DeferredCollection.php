<?php

declare(strict_types=1);

namespace DeferredCollection;

use ArrayIterator;
use DeferredCollection\Processor\ProcessorInterface;
use IteratorAggregate;
use JsonSerializable;
use LogicException;
use RuntimeException;
use Traversable;

/**
 * @method $this map(callable $callback, bool $mapKeys = false)
 * @method $this filter(callable $predicate)
 * @method $this reduce(callable $callback, mixed $initialValue = null)
 * @method $this instantiate(string $modelClassName, string $indexBy = '')
 * @method $this matchProperty(string $propertyName, mixed|callable $matcher)
 * @method $this pluckProperty(string $propertyName)
 * @method $this max(string|callable|null $iteratee = null)
 * @method $this min(string|callable|null $iteratee = null)
 */
class DeferredCollection implements
    IteratorAggregate,
    JsonSerializable
{
    /** @var IterationSubject */
    private $iterationSubject;

    /** @var ProcessorInterface[] */
    private $processors = [];

    /** @var iterable */
    private $processedIterable;

    /** @var iterable */
    private $processedValue;

    /**
     * @param iterable|callable|null $iterationSubject
     */
    public function __construct($iterationSubject = null)
    {
        if ($iterationSubject !== null) {
            $this->setIterationSubject($iterationSubject);
        }
    }

    /**
     * @param iterable|callable $iterationSubject
     *
     * @return DeferredCollection
     */
    public function setIterationSubject($iterationSubject): self
    {
        $this->assertNotProcessed();
        $this->iterationSubject = new IterationSubject($iterationSubject);

        return $this;
    }

    /**
     * @throws LogicException
     */
    private function assertNotProcessed(): void
    {
        if ($this->hasBeenAlreadyProcessed()) {
            throw new LogicException(get_class($this) . ' cannot be modified after it is processed');
        }
    }

    /**
     * @return bool
     */
    private function hasBeenAlreadyProcessed(): bool
    {
        return $this->processedIterable !== null;
    }

    /**
     * @return iterable|callable
     */
    public function getIterationSubject()
    {
        return $this->iterationSubject ? $this->iterationSubject->getSubject() : null;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->doesLastProcessorReturnsSingleValue()
            ? $this->getValue()
            : $this->toArray();
    }

    /**
     * @return bool
     */
    private function doesLastProcessorReturnsSingleValue(): bool
    {
        return count($this->processors)
            ? $this->processors[count($this->processors) - 1]->isSingleValue()
            : false;
    }

    /**
     * Get the value if the processing result is a single value.
     *
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function getValue($defaultValue = null)
    {
        if (!isset($this->processedValue)) {
            $this->processedValue = $this->extractSingleValueFromIterable($defaultValue);
        }

        return $this->processedValue;
    }

    /**
     * @param $defaultValue
     *
     * @return mixed
     */
    private function extractSingleValueFromIterable($defaultValue)
    {
        if (is_array($iterable = $this->getProcessedIterable())) {
            return count($iterable) ? reset($iterable) : $defaultValue;
        }
        foreach ($iterable as $value) {
            return $value;
        }

        return $defaultValue;
    }

    /**
     * @return iterable
     */
    private function getProcessedIterable(): iterable
    {
        if ($this->processedIterable === null) {
            $this->processedIterable = $this->process();
        }

        return $this->processedIterable;
    }

    /**
     * @throws RuntimeException
     *
     * @return iterable
     */
    private function process(): iterable
    {
        if (!$this->iterationSubject) {
            throw new RuntimeException('No iteration data were specified for ' . __CLASS__);
        }
        $iterable = $this->iterationSubject->getIterable();

        foreach ($this->processors as $processor) {
            $iterable = $processor->process($iterable);
        }

        return $iterable;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if (is_array($iterable = $this->getProcessedIterable())) {
            return $iterable;
        }

        return $this->iteratorToArray($iterable);
    }

    /**
     * @param Traversable $iterator
     *
     * @return array
     */
    private function iteratorToArray(Traversable $iterator): array
    {
        $array = [];
        $first = true;

        foreach ($iterator as $key => $value) {
            if ($first) {
                // Save first value for later use in getValue()
                $this->processedValue = $value;
                $first = false;
            }
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        $iterable = $this->getProcessedIterable();

        return is_array($iterable) ? new ArrayIterator($iterable) : $iterable;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @throws RuntimeException
     *
     * @return DeferredCollection
     */
    public function __call(string $method, array $arguments): self
    {
        if ($processorClassName = DeferredCollectionMethodRegistry::findProcessorClass($method)) {
            $this->pushProcessor(new $processorClassName(...$arguments));

            return $this;
        }
        throw new RuntimeException('Call to undefined method ' . get_class($this) . "::$method");
    }

    /**
     * @param ProcessorInterface $processor
     */
    private function pushProcessor(ProcessorInterface $processor): void
    {
        $this->assertNotProcessed();
        $this->processors[] = $processor;
    }
}
