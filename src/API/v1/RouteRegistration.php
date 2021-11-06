<?php
/**
 * RouteRegistration.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\API\v1;

use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints\Activations;
use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints\ApiEndpoint;
use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints\Releases\LatestReleases;

class RouteRegistration
{
    const API_NAMESPACE = 'af/edd-sl/v1';

    private array $routes = [
        Activations::class,
        LatestReleases::class,
    ];

    public function registerRoutes(): void
    {
        foreach ($this->routes as $route) {
            /** @var ApiEndpoint $route */
            $route = new $route();
            $route->register();
        }
    }

}
