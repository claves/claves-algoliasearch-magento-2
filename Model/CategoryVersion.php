<?php

namespace Algolia\AlgoliaSearch\Model;

use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;
use Algolia\AlgoliaSearch\Model\ResourceModel\CategoryVersion as CategoryVersionResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class CategoryVersion extends AbstractExtensibleModel implements IdentityInterface, CategoryVersionInterface
{
    const CACHE_TAG = 'algoliasearch_category_version';
    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'algoliasearch_category_version';

    protected function _construct()
    {
        $this->_init(ResourceModel\CategoryVersion::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getCategoryId(): int
    {
        return $this->getData(CategoryVersionResource::CATEGORY_ID);
    }

    public function setCategoryId(int $categoryId): CategoryVersionInterface
    {
        return $this->setData(CategoryVersionResource::CATEGORY_ID, $categoryId);
    }

    public function getStoreId(): int
    {
        return $this->getData(CategoryVersionResource::STORE_ID);
    }

    public function setStoreId(int $storeId): CategoryVersionInterface
    {
        return $this->setData(CategoryVersionResource::STORE_ID, $storeId);
    }

    public function getOldValue(): string
    {
        return $this->getData(CategoryVersionResource::OLD_VALUE);
    }

    public function setOldValue(string $val): CategoryVersionInterface
    {
        return $this->setData(CategoryVersionResource::OLD_VALUE, $val);
    }

    public function getNewValue(): string
    {
        return $this->getData(CategoryVersionResource::NEW_VALUE);
    }

    public function setNewValue(string $val): CategoryVersionInterface
    {
        return $this->setData(CategoryVersionResource::NEW_VALUE, $val);
    }

    public function getUpdatedAt(): string
    {
        return $this->getData(CategoryVersionResource::UPDATED_AT);
    }

    public function setUpdatedAt(?string $val): CategoryVersionInterface
    {
        return $this->setData(CategoryVersionResource::UPDATED_AT, $val);
    }
}
