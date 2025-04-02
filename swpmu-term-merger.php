<?php
/**
 * Plugin Name:       SWPMU Term Merger
 * Plugin URI:        https://github.com/seoul-wordpress-meetup/swpmu-term-merger
 * Description:       Term merger plugin for WordPress.
 * Author:            Seoul WordPress Meetup
 * Author URI:        https://www.meetup.com/ko-KR/wordpress-meetup-seoul/
 * License:           GPL-v2-or-later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.9
 * Requires PHP:      8.0
 * Tested up to:      6.7
 * Textdomain:        swpmu-term-merger
 * Version:           1.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

const SWPMU_TERM_MERGER_MAIN    = __FILE__;
const SWPMU_TERM_MERGER_VERSION = '1.0.1';

// Call autoloader earlier than composer autoload.
require_once __DIR__ . '/inc/functions.php';
spl_autoload_register('swpmuAutoloader');

require_once __DIR__ . '/vendor/autoload.php';

swpmuTmgr();
