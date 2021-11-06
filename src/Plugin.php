<?php
/**
 * Plugin.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 * @since     1.0
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI;

use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\RouteRegistration;

class Plugin
{

    public function boot(): void
    {
        add_action('rest_api_init', static function () {
            if (class_exists('EDD_Software_Licensing')) {
                (new RouteRegistration())->registerRoutes();
            }
        });
    }

}
