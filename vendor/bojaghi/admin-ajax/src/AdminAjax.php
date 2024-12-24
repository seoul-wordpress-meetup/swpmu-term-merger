<?php

namespace SWM\TermMerger\Vendor\Bojaghi\AdminAjax;

class AdminAjax extends SubmitBase
{
    public function getPrivAction($tag): string
    {
        return "wp_ajax_$tag";
    }

    public function getNoPrivAction($tag): string
    {
        return "wp_ajax_nopriv_$tag";
    }
}
