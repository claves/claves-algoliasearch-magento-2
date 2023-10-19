<?php

namespace Algolia\AlgoliaSearch\Api;

use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;

interface CategoryVersionRepositoryInterface
{
    /**
     * @param int $id
     * @return CategoryVersionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id) : CategoryVersionInterface;

    /**
     * @return CategoryVersionInterface
     */
    public function getNew() : CategoryVersionInterface;

    /**
     * @param CategoryVersionInterface $version
     * @return CategoryVersionInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CategoryVersionInterface $version): CategoryVersionInterface;
}
