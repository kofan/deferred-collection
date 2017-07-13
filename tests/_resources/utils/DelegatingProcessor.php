<?php

declare(strict_types=1);

namespace DeferredCollection\TestUtils;

use DeferredCollection\Processor\ProcessorInterface;
use InvalidArgumentException;

class DelegatingProcessor implements ProcessorInterface
{
    /** @var array|callable */
    private $callback;
    /** @var bool */
    private $isSingleValue;

    /**
     * @param callable|object $callback      Callable or the object that is going to be called with with __invoke
     * @param bool            $isSingleValue
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag) - $isSingleValue
     */
    public function __construct($callback, bool $isSingleValue = false)
    {
        if (!is_callable($callback)) {
            if (is_object($callback)) {
                $callback = [$callback, '__invoke'];
            } else {
                throw new InvalidArgumentException('$callback should be either callable or an object');
            }
        }

        $this->callback = $callback;
        $this->isSingleValue = $isSingleValue;
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleValue(): bool
    {
        return $this->isSingleValue;
    }

    /**
     * {@inheritdoc}
     */
    public function process(iterable $iterable): iterable
    {
        return ($this->callback)($iterable);
    }
}
