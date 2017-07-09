<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

class PluckPropertyProcessor extends AbstractMultiValueProcessor
{
    use PropertyProcessorTrait;

    /**
     * @param string $propertyName
     */
    public function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        foreach ($iterable as $key => $item) {
            $propertyValue = $this->getPropertyValue($item);
            yield $key => $propertyValue;
        }
    }
}
