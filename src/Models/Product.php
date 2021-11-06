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

    protected bool $allowBetaVersions = false;

    /**
     * Whether to accept a beta version.
     *
     * @param  bool  $allowBetaVersions
     *
     * @return $this
     */
    public function withBeta(bool $allowBetaVersions): self
    {
        $this->allowBetaVersions = $allowBetaVersions;

        return $this;
    }

    /**
     * Minimal array used in `LatestRelease` API requests.
     *
     * @return array
     */
    public function toVersionCheckArray(): array
    {
        $newVersion = $stableVersion = $this->get_version();
        if ($this->allowBetaVersions && $this->has_beta()) {
            $newVersion = $this->get_beta_version();
        }

        return [
            'id'             => $this->ID,
            'new_version'    => $newVersion,
            'stable_version' => $stableVersion,
            'name'           => $this->post_title,
            'last_updated'   => $this->post_modified_gmt,
        ];
    }

    /**
     * Full array of the product. Used when retrieving full information, including
     * changelog and description. ("View More Details" link in WordPress.)
     *
     * @return array
     */
    public function toArray(): array
    {
        return wp_parse_args([], $this->toVersionCheckArray());
    }

}
