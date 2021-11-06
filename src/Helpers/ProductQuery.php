<?php
/**
 * ProductQuery.php
 *
 * @package   edd-sl-api
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\EDD\SoftwareLicensingAPI\Helpers;

use AshleyFae\EDD\SoftwareLicensingAPI\Models\Product;

class ProductQuery
{
    protected \wpdb $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->getPostObjectsFromIds(
            $this->getProductIds()
        );
    }

    protected function getProductIds(): array
    {
        $productIds = $this->wpdb->get_col(
            "SELECT post_id FROM {$this->wpdb->postmeta}
            WHERE meta_key = '_edd_sl_enabled'"
        );

        return array_unique($productIds);
    }

    protected function getPostObjectsFromIds(array $productIds): array
    {
        $products = [];

        if (empty($productIds)) {
            return $products;
        }

        _prime_post_caches($productIds, false, true);

        foreach($productIds as $productId) {
            $products[] = new Product($productId);
        }

        return $products;
    }

}
