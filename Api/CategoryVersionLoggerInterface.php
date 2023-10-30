<?php

namespace Algolia\AlgoliaSearch\Api;

use Magento\Catalog\Model\Category;

interface CategoryVersionLoggerInterface
{
    /**
     * @param Category $category
     * @param int $storedId
     * @return void
     */
    public function logCategoryChange(Category $category, int $storedId = 0): void;
}
