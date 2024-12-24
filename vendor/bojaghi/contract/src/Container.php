<?php

namespace SWM\TermMerger\Vendor\Bojaghi\Contract;

use SWM\TermMerger\Vendor\Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    public function call(callable|array|string $callable, array|callable $args = []);
}
