<?php

namespace Algolia\AlgoliaSearch\Api\Data;

use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;

interface CategoryVersionAttributeInterface
{
    /**
     * Does this category have versions?
     * @return bool
     */
    public function hasVersions() : boolean;

    /**
     * Get all versions of this category
     * @return CategoryVersionInterface[]
     */
    public function getVersions() : array;

    /**
     * Get all possible request paths that can be used as category filters
     * @return string[]
     */
    public function getSearchFilters() : array;

}
