<?php
/**
 * Product.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\Models;

use AshleyFae\EDD\SoftwareLicensingAPI\Traits\Serializable;

class Product extends \EDD_SL_Download
{

    use Serializable;

    protected bool $returnBetaVersions = false;

    public function withBeta(bool $returnBetaVersions): self
    {
        $this->returnBetaVersions = $returnBetaVersions;

        return $this;
    }

    public function toArray(): array
    {
        $newVersion = $stableVersion = $this->get_version();

        return [
            'id'             => $this->ID,
            'new_version'    => $newVersion,
            'stable_version' => $stableVersion,
            'name'           => $this->post_title,
            'last_updated'   => $this->post_modified_gmt,
        ];
    }

}
