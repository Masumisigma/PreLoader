<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Sigma\PreloaderChildProductIDs\Loader;

use EcomDev\ProductDataPreLoader\DataService\DataLoader;
use EcomDev\ProductDataPreLoader\DataService\ScopeFilter;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\ResourceConnection;

class ConfigurableChildProductIds implements DataLoader
{
    public const DATA_KEY = 'configurable_child_ids';

    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /** @inheritDoc */
    public function load(ScopeFilter $filter, array $products): array
    {
        $configurableProductIds = [];

        foreach ($products as $product) {
            if ($product->isType(Configurable::TYPE_CODE)) {
                $configurableProductIds[] = $product->getId();
            }
        }

        if (!$configurableProductIds) {
            return [];
        }
       
        $connection = $this->resourceConnection->getConnection('catalog');

        $relationTable = $this->resourceConnection->getTableName('catalog_product_super_link','catalog');
        $select = $connection->select();

        $select
            ->from(['relation' => $relationTable], ['parent_id','product_id'])
            ->where('relation.parent_id IN(?)',$configurableProductIds)
        ;
        $result=[];
        foreach ($select->query() as $row)
        {
            $result[$row['parent_id']][] = $row['product_id'];
        }
        return $result;
    }

    public function isApplicable(string $type): bool
    {
        return true;
    }
}
