<?php

namespace Algolia\AlgoliaSearch\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CategoryVersionInterface extends ExtensibleDataInterface
{
    const CATEGORY_ID = 'entity_id';
    const STORE_ID = 'store_id';
    const OLD_VALUE = 'old_value';
    const NEW_VALUE = 'new_value';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const INDEXED_AT = 'indexed_at';
    const RESOLVED_AT = 'resolved_at';

    public function getCategoryId(): int;
    public function setCategoryId(int $categoryId): CategoryVersionInterface;

    public function getStoreId(): int;
    public function setStoreId(int $storeId): CategoryVersionInterface;

    public function getOldValue(): string;
    public function setOldValue(string $val): CategoryVersionInterface;

    public function getNewValue(): string;
    public function setNewValue(string $val): CategoryVersionInterface;

    public function getUpdatedAt(): string;
    public function setUpdatedAt(?string $val): CategoryVersionInterface;

}
