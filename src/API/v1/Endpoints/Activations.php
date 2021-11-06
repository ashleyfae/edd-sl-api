<?php
/**
 * Activations.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\API\v1\Endpoints;

use AshleyFae\EDD\SoftwareLicensingAPI\API\v1\RouteRegistration;
use AshleyFae\EDD\SoftwareLicensingAPI\Traits\GetsLicenseByKey;
use AshleyFae\EDD\SoftwareLicensingAPI\Exceptions\ApiException;
use AshleyFae\EDD\SoftwareLicensingAPI\Exceptions\ModelNotFoundException;

class Activations implements ApiEndpoint
{
    use GetsLicenseByKey;

    public function register(): void
    {
        register_rest_route(
            RouteRegistration::API_NAMESPACE,
            'licenses/activations',
            [
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'activateLicense'],
                    'permission_callback' => '__return_true',
                    'args'                => $this->getActivationArgs(),
                ],
                [
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'deactivateLicense'],
                    'permission_callback' => '__return_true',
                    'args'                => $this->getActivationArgs(),
                ]
            ]
        );
    }

    private function getActivationArgs(): array
    {
        return [
            'license'     => [
                'required'          => true,
                'type'              => 'string',
                'validate_callback' => function ($param, $request, $key) {
                    try {
                        $this->getLicenseIdByKey($param);

                        return true;
                    } catch (ModelNotFoundException $e) {
                        return false;
                    }
                },
                'sanitize_callback' => function ($param, $request, $key) {
                    return wp_strip_all_tags($param);
                }
            ],
            'product_id'  => [
                'required'          => true,
                'type'              => 'integer',
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param) && get_post_type($param) === 'download';
                },
                'sanitize_callback' => function ($param, $request, $key) {
                    return intval($param);
                }
            ],
            'url'         => [
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => function ($param, $request, $key) {
                    return esc_url_raw($param);
                }
            ],
            'environment' => [
                'required'          => false,
                'type'              => 'string',
                'sanitize_callback' => function ($param, $request, $key) {
                    return sanitize_text_field(strtolower($param));
                }
            ]
        ];
    }

    public function activateLicense(\WP_REST_Request $request): \WP_REST_Response
    {
        $result = edd_software_licensing()->activate_license([
            'key'         => $request->get_param('license'),
            'item_id'     => $request->get_param('product_id'),
            'url'         => $request->get_param('url'),
            'environment' => $request->get_param('environment'),
        ]);

        try {
            if (empty($result['success'])) {
                $data = $this->getErrorDataFromResult($result);

                throw new ApiException(
                    $data['error_code'] ?? '',
                    $data['error_message'] ?? '',
                    400
                );
            }

            try {
                $license = $this->getLicense($request->get_param('license'));
            } catch (ModelNotFoundException $e) {
                throw new ApiException(
                    'invalid_license',
                    __('This license key does not exist.', 'edd-sl-api'),
                    400,
                    $e
                );
            }

            return new \WP_REST_Response([
                'activated' => true,
                'license'   => $license->toArray(),
            ], 201);
        } catch (ApiException $e) {
            return new \WP_REST_Response([
                'activated'     => false,
                'error_code'    => $e->getErrorCode(),
                'error_message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function deactivateLicense(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $result = edd_software_licensing()->deactivate_license([
                'key'         => $request->get_param('license'),
                'item_id'     => $request->get_param('product_id'),
                'url'         => $request->get_param('url'),
                'environment' => $request->get_param('environment'),
            ]);

            if (! $result) {
                throw new ApiException();
            }

            try {
                $license = $this->getLicense($request->get_param('license'));
            } catch (ModelNotFoundException $e) {
                throw new ApiException(
                    'invalid_license',
                    __('This license key does not exist.', 'edd-sl-api'),
                    400,
                    $e
                );
            }

            return new \WP_REST_Response([
                'deactivated' => true,
                'license'     => $license->toArray(),
            ], 201);
        } catch (ApiException $e) {
            return new \WP_REST_Response([
                'deactivated'   => false,
                'error_code'    => $e->getErrorCode(),
                'error_message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    protected function getErrorDataFromResult(array $result): array
    {
        $errorData = [
            'error_code'    => $result['error'] ?? 'unknown_error',
            'error_message' => __('An unknown error has occurred.', 'edd-sl-api'),
        ];

        switch (strtolower($result['error']) ?? null) {
            case 'missing' :
                $errorData['error_code']    = 'invalid_license';
                $errorData['error_message'] = __('This license key does not exist.', 'edd-sl-api');
                break;
            case 'missing_url' :
                $errorData['error_message'] = __('A URL is required for activation.', 'edd-sl-api');
                break;
            case 'license_not_activable' :
                $errorData['error_code']    = 'bundle_activation_disallowed';
                $errorData['error_message'] = __('Bundle license activations are not permitted.', 'edd-sl-api');
                break;
            case 'disabled' :
                $errorData['error_code']    = 'license_disabled';
                $errorData['error_message'] = __('This license key cannot be activated.', 'edd-sl-api');
                break;
            case 'no_activations_left' :
                $errorData['error_message'] = __('This license has reached its activation limit.', 'edd-sl-api');
                break;
            case 'expired' :
                $errorData['error_code']    = 'license_expired';
                $errorData['error_message'] = __('This license key has expired.', 'edd-sl-api');
                break;
            case 'key_mismatch' :
                $errorData['error_code']    = 'license_key_mismatch';
                $errorData['error_message'] = __('The provided license key does not match our records.', 'edd-sl-api');
                break;
            case 'item_name_mismatch' :
                $errorData['error_message'] = __(
                    'The provided product name does not match the item this license key is assigned to.',
                    'edd-sl-api'
                );
                break;
        }

        return $errorData;
    }
}
