<?php
/**
 * License.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\Models;

use AshleyFae\EDD\SoftwareLicensingAPI\Traits\Serializable;

class License extends \EDD_SL_License
{

    use Serializable;

    public function toArray(): array
    {
        $activation_count      = $this->activation_count;
        $activations_remaining = 'unlimited';
        if ($this->activation_limit > 0) {
            $activations_remaining = (int) $this->activation_limit - (int) $activation_count;
        }
        $customer = $this->customer;

        return [
            'id'                    => $this->id,
            'license_key'           => $this->license_key,
            'status'                => $this->status,
            'product_id'            => $this->download_id,
            'price_id'              => $this->price_id,
            'order_id'              => $this->payment_id,
            'created_at'            => $this->date_created,
            'expires_at'            => $this->expiration ? date('Y-m-d H:i:s', $this->expiration) : null,
            'number_activations'    => $activation_count,
            'activation_limit'      => $this->activation_limit > 0 ? $this->activation_limit : 'unlimited',
            'activations_remaining' => $activations_remaining,
            'customer'              => [
                'id'    => $customer ? $customer->id : null,
                'name'  => $customer ? $customer->name : null,
                'email' => $customer ? $customer->email : null
            ]
        ];
    }

}
