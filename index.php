<?php
/**
 * Plugin Name:       SWM Term Merger
 * Plugin URI:        https://github.com/seoul-wordpress-meetup/swm-term-merger
 * Description:       Term merger plugin for WordPress.
 * Author:            Seoul WordPress Meetup
 * Author URI:        https://www.meetup.com/ko-KR/wordpress-meetup-seoul/
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Textdomain:        swm-term-merger
 * Version:           0.9.0
 */

const SWM_TERM_MERGER_MAIN    = __FILE__;
const SWM_TERM_MERGER_VERSION = '0.9.0';

// Call autoloader earlier than composer autoload.
require_once __DIR__ . '/inc/functions.php';
spl_autoload_register('swmTmgrAutoloader');

require_once __dir__ . '/vendor/autoload.php';

if (!defined('ABSPATH')) {
    exit;
}

swmTmgr();
