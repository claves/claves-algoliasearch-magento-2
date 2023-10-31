<?php

namespace Algolia\AlgoliaSearch\Api;

use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CategoryVersionLoggerInterface
{
    /**
     * @param Category $category
     * @param int $storedId
     * @return void
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     */
    public function logCategoryChange(Category $category, int $storedId = 0): void;
}
