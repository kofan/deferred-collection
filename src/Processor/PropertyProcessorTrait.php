<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

use InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

trait PropertyProcessorTrait
{
    /** @var string */
    private $propertyName;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    /**
     * AbstractPropertyProcessor constructor.
     *
     * @param $propertyName
     */
    public function __construct(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @param array|object $item
     *
     * @return mixed
     */
    private function getPropertyValue($item)
    {
        if (!is_array($item) && !is_object($item)) {
            throw new InvalidArgumentException('Invalid collection item type - cannot access the property');
        }

        return $this->getPropertyAccessor()->getValue($item, $this->propertyName);
    }

    /**
     * @return PropertyAccessor
     */
    private function getPropertyAccessor()
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
