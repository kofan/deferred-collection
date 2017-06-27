<?php

namespace DeferredCollection;

use InvalidArgumentException;

class IterationSubject
{
    /** @var iterable|callable */
    private $subject;

    /** @var iterable */
    private $iterable;

    /** @var callable */
    private $callable;

    /**
     * @param iterable|callable $subject
     */
    public function __construct($subject)
    {
        if (is_iterable($subject)) {
            $this->iterable = $subject;
        } elseif (is_callable($subject)) {
            $this->callable = $subject;
        } else {
            throw new InvalidArgumentException(__CLASS__ . ' accepts either iterable or callable object');
        }

        $this->subject = $subject;
    }

    /**
     * @return callable|iterable
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return iterable
     */
    public function getIterable() : iterable
    {
        if ($this->iterable === null) {
            $this->iterable = $this->getIterableFromCallable();
        }
        return $this->iterable;
    }

    /**
     * @return iterable
     */
    private function getIterableFromCallable() : iterable
    {
        $callable = $this->callable;
        $iterable = $callable();

        if (!is_iterable($iterable)) {
            throw new InvalidArgumentException(__CLASS__ . ' callable subject should return iterable');
        }
        return $iterable;
    }
}
