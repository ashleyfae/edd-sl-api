<?php
/**
 * GetsLicenseByKey.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\Traits;

use AshleyFae\EDD\SoftwareLicensingAPI\Models\License;
use AshleyFae\EDD\SoftwareLicensingAPI\Exceptions\ModelNotFoundException;

trait GetsLicenseByKey
{

    /**
     * Returns the ID of a license, given its key.
     *
     * @param  string  $licenseKey
     *
     * @return int ID of the license.
     * @throws ModelNotFoundException
     */
    public function getLicenseIdByKey(string $licenseKey): int
    {
        $id = edd_software_licensing()->licenses_db->get_column_by(
            'id',
            'license_key',
            sanitize_text_field($licenseKey)
        );

        if (is_numeric($id)) {
            return (int) $id;
        }

        throw new ModelNotFoundException("License key {$licenseKey} not found.");
    }

    /**
     * Returns a license object by key.
     *
     * @param  string  $licenseKey
     *
     * @return License
     * @throws ModelNotFoundException
     */
    public function getLicense(string $licenseKey): License
    {
        return new License($this->getLicenseIdByKey($licenseKey));
    }

}
