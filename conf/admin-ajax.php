<?php
/**
 * Admin AJAX request configuration.
 *
 * @noinspection PhpClassConstantAccessedViaChildClassInspection
 */

use SWM\TermMerger\Vendor\Bojaghi\AdminAjax\AdminAjax;

if (!defined('ABSPATH')) {
    exit;
}

return [
    // Request
    // 0th: action
    // 1st: callback
    // 2nd: permission
    // 3rd: nonce variable name
    // 4th: priority (omit)

    /**
     * Get terms (by taxonomy)
     *
     * @uses \SWM\TermMerger\Modules\AjaxHandler::getTerms()
     */
    ['swmTmgr/getTerms', 'tmgr/ajaxHandler@getTerms', AdminAjax::ONLY_PRIV, '_swm_tmgr_nonce'],

    /**
     * Execute term merge
     *
     * @uses \SWM\TermMerger\Modules\AjaxHandler::mergeTerms()
     */
    ['swmTmgr/mergeTerms', 'tmgr/ajaxHandler@mergeTerms', AdminAjax::ONLY_PRIV, '_swm_tmgr_nonce'],
];
