<?php
/**
 * Serializable.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\Traits;

trait Serializable
{

    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
