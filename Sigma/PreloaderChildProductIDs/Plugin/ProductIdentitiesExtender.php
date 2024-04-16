<?php
/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */
declare(strict_types=1);

namespace Sigma\PreloaderChildProductIDs\Plugin;

use Sigma\PreloaderChildProductIDs\Loader\ConfigurableChildProductIds;
use EcomDev\ProductDataPreLoader\DataService\LoadService;
use Magento\Catalog\Model\Product;

class ProductIdentitiesExtender
{
    private LoadService $loadService;

    public function __construct(LoadService $loadService)
    {
        $this->loadService = $loadService;
    }

    public function afterGetIdentities(Product $subject, array $identities)
    {
        $productId = (int)$subject->getId();
        
        if ($this->loadService->has($productId, ConfigurableChildProductIds::DATA_KEY)) {
            return array_merge($identities,$this->loadService->get($productId, ConfigurableChildProductIds::DATA_KEY)) ;
        
        }
        return $identities;
    }
}
