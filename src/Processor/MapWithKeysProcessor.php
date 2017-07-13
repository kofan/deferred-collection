<?php

declare(strict_types=1);

namespace DeferredCollection\Processor;

class MapWithKeysProcessor extends MapProcessor
{
    /**
     * @return bool
     */
    protected function shouldMapKeys(): bool
    {
        return true;
    }
}
