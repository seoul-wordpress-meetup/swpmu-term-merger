<?php
/**
 * Admin AJAX request configuration.
 *
 * @noinspection PhpClassConstantAccessedViaChildClassInspection
 */

use SWPMU\TermMerger\Vendor\Bojaghi\AdminAjax\AdminAjax;

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
     * @uses \SWPMU\TermMerger\Modules\AjaxHandler::getTerms()
     */
    [
        'swpmuTmgr/getTerms',
        'swpmuTmgr/ajaxHandler@getTerms',
        AdminAjax::ONLY_PRIV,
        '_swpmu_tmgr_nonce',
    ],

    /**
     * Execute term merge
     *
     * @uses \SWPMU\TermMerger\Modules\AjaxHandler::mergeTerms()
     */
    [
        'swpmuTmgr/mergeTerms',
        'swpmuTmgr/ajaxHandler@mergeTerms',
        AdminAjax::ONLY_PRIV,
        '_swpmu_tmgr_nonce',
    ],
];
