<?php

namespace SWM\TermMerger\Modules;

use SWM\TermMerger\Vendor\Bojaghi\Contract\Module;

class Translation implements Module
{
    public function __construct()
    {
        $this->loadTextdomain();
    }

    private function loadTextdomain(): void
    {
        load_plugin_textdomain(
            'swm-term-merger',
            false,
            dirname(plugin_basename(SWM_TERM_MERGER_MAIN)) . '/languages',
        );
    }
}