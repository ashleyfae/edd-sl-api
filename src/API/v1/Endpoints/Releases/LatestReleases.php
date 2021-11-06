<?php
/**
 * LatestReleases.php
 *
 * Returns a list of all products, along with the version number of the latest release.
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints\Releases;

use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints\ApiEndpoint;
use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\RouteRegistration;
use AshleyFae\EDD\SoftwareLicensingAPI\Helpers\ProductQuery;
use AshleyFae\EDD\SoftwareLicensingAPI\Models\Product;

class LatestReleases implements ApiEndpoint
{

    public function register(): void
    {
        register_rest_route(
            RouteRegistration::API_NAMESPACE,
            'latest-releases',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'handle'],
                    'permission_callback' => '__return_true',
                    'args'                => [
                        'beta' => [
                            'required'          => false,
                            'default'           => false,
                            'type'              => 'boolean',
                            'sanitize_callback' => function ($param, $request, $key) {
                                return filter_var($param, FILTER_VALIDATE_BOOLEAN);
                            }
                        ]
                    ]
                ]
            ]
        );
    }

    public function handle(\WP_REST_Request $request): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'products' => $this->getProducts($request->get_param('beta')),
        ]);
    }

    protected function getProducts(bool $returnBetaVersions): array
    {
        $query = new ProductQuery();

        return array_map(function (Product $product) use ($returnBetaVersions) {
            return $product->withBeta($returnBetaVersions)->toVersionCheckArray();
        }, $query->getProducts());
    }
}
