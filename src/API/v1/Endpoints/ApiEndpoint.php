<?php
/**
 * ApiEndpoint.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints;

interface ApiEndpoint
{

    /**
     * Registers the route(s).
     *
     * @return void
     */
    public function register(): void;

}
