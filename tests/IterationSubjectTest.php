<?php

namespace DeferredCollection;

use ArgumentCountError;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IterationSubjectTest extends TestCase
{
    public function testCannotCreateWithoutArguments() : void
    {
        $this->expectException(ArgumentCountError::class);
        new IterationSubject();
    }

    public function testCannotCreateWithNull()
    {
        $this->expectException(InvalidArgumentException::class);
        new IterationSubject(null);
    }

    public function testCannotCreateWithNonIterableAndNonCallable() : void
    {
        $this->expectException(InvalidArgumentException::class);
        new IterationSubject('just a string');
    }

    public function testCanWorkWithIterable() : void
    {
        $iterable = [1, 2, 3];
        $iterationSubject = new IterationSubject($iterable);

        $this->assertSame($iterationSubject->getSubject(), $iterable);
        $this->assertSame($iterationSubject->getIterable(), $iterable);
    }

    public function testCanWorkWithCallable() : void
    {
        $iterable = [1, 2, 3];
        $callable = function () use ($iterable) { return $iterable; };
        $iterationSubject = new IterationSubject($callable);

        $this->assertSame($iterationSubject->getSubject(), $callable);
        $this->assertSame($iterationSubject->getIterable(), $iterable);
    }

    public function testDoesNotWorkWithCallableWhichIsNotReturningIterable() : void
    {
        $callable = function () { return 100; };
        $iterationSubject = new IterationSubject($callable);

        $this->expectException(InvalidArgumentException::class);
        $iterationSubject->getIterable();
    }
}
