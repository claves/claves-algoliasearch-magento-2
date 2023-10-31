<?php

namespace Algolia\AlgoliaSearch\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CategoryVersionInterface extends ExtensibleDataInterface
{

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
