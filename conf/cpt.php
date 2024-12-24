<?php
/**
 * Custom posts configuration.
 */

use SWM\TermMerger\Supports\Workspace;

if (!defined('ABSPATH')) {
    exit;
}

return [
    // CPT: workspace 
    [
        // Post type name. Maximum 20 characters.
        Workspace::POST_TYPE,
        // Post type arguments.
        Workspace::getCptArgs(),
    ],
];
