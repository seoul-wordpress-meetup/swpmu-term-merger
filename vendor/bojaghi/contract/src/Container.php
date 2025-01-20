<?php

namespace SWPMU\TermMerger\Vendor\Bojaghi\Contract;

use SWPMU\TermMerger\Vendor\Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    public function call(callable|array|string $callable, array|callable $args = []);
}
