<?php
/**
 * Plugin Name: EDD - Software Licensing API
 * Plugin URI: https://github.com/ashleyfae/edd-sl-api
 * Description: A better API for EDD Software Licensing.
 * Version: 1.0
 * Author: Ashley Gibson
 * Author URI: https://github.com/ashleyfae/
 * License: GPL2 License
 * URI: https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

if (version_compare(phpversion(), '7.4', '<')) {
    return;
}

if (version_compare(get_bloginfo('version'), '5.3', '<')) {
    return;
}

require_once __DIR__.'/vendor/autoload.php';

(new \AshleyFae\EDD\SoftwareLicensingAPI\Plugin())->boot();
