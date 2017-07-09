<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

class MatchPropertyProcessor extends AbstractMultiValueProcessor
{
    use PropertyProcessorTrait;

    /** @var callable */
    private $matchCallback;

    /** @var mixed */
    private $matchValue;

    /**
     * @param string         $propertyName
     * @param mixed|callable $matcher
     */
    public function __construct(string $propertyName, $matcher)
    {
        $this->propertyName = $propertyName;

        if (is_callable($matcher)) {
            $this->matchCallback = $matcher;
        } else {
            $this->matchValue = $matcher;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        foreach ($iterable as $key => $item) {
            $propertyValue = $this->getPropertyValue($item);

            if ($this->doesPropertyValueMatch($propertyValue)) {
                yield $key => $item;
            }
        }
    }

    /**
     * @param mixed $propertyValue
     *
     * @return bool
     */
    private function doesPropertyValueMatch($propertyValue): bool
    {
        return isset($this->matchCallback)
            ? ($this->matchCallback)($propertyValue)
            : ($propertyValue === $this->matchValue);
    }
}
