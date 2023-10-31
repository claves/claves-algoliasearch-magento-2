<?php

namespace Algolia\AlgoliaSearch\Model\CategoryVersion;

use Algolia\AlgoliaSearch\Api\Data\CategoryVersionAttributeInterface;

class CategoryVersionAttribute implements CategoryVersionAttributeInterface
{
    /** @var int|null */
    protected $storeId;

    /** @var int */
    protected $categoryId;

    /**
     * @inheritDoc
     */
    public function load(int $categoryId, int $storeId = null) : CategoryVersionAttributeInterface
    {
        $this->categoryId = $categoryId;
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasVersions(int $storeId = null): bool
    {
        // TODO: Implement hasVersions() method.
    }

    /**
     * @inheritDoc
     */
    public function getVersions(int $storeId = null): array
    {
        // TODO: Implement getVersions() method.
    }

    /**
     * @inheritDoc
     */
    public function getSearchFilters(int $storeId = null): array
    {
        // TODO: Implement getSearchFilters() method.
    }
}
