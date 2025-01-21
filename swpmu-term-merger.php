<?php
/**
 * Plugin Name:       SWPMU Term Merger
 * Plugin URI:        https://github.com/seoul-wordpress-meetup/swpmu-term-merger
 * Description:       Term merger plugin for WordPress.
 * Author:            Seoul WordPress Meetup
 * Author URI:        https://www.meetup.com/ko-KR/wordpress-meetup-seoul/
 * Requires at least: 5.9.0
 * Requires PHP:      8.0
 * Textdomain:        swpmu-term-merger
 * License:           GPL-v2-or-later
 * Version:           0.9.1
 */

const SWPMU_TERM_MERGER_MAIN    = __FILE__;
const SWPMU_TERM_MERGER_VERSION = '0.9.1';

// Call autoloader earlier than composer autoload.
require_once __DIR__ . '/inc/functions.php';
spl_autoload_register('swpmuAutoloader');

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('ABSPATH')) {
    exit;
}

swpmuTmgr();
