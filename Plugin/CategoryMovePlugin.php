<?php

namespace Algolia\AlgoliaSearch\Plugin;

use Algolia\AlgoliaSearch\Api\CategoryVersionLoggerInterface;
use Algolia\AlgoliaSearch\Helper\Logger;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CategoryMovePlugin
{
    protected $logger;

    /** @var StoreManagerInterface */
    protected StoreManagerInterface $storeManager;

    /** @var CategoryVersionLoggerInterface */
    protected CategoryVersionLoggerInterface $categoryVersionLogger;

    public function __construct(
        Logger                         $logger,
        StoreManagerInterface          $storeManager,
        CategoryVersionLoggerInterface $categoryVersionLogger
    )
    {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->categoryVersionLogger = $categoryVersionLogger;
    }

    /**
     * @param Category $subject
     * @param Category $result
     * @param int $parentId
     * @param int|null $afterCategoryId
     * @return Category
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function afterMove(Category $subject, Category $result, int $parentId, null|int $afterCategoryId)
    {
        $this->logger->log("Moving the category to $parentId with after id $afterCategoryId");
        $this->logger->log("Original category path " . $result->getOrigData('path'));
        $this->logger->log("New category path: " . $result->getData('path'));

        $storeId = $this->storeManager->getStore()->getId();
        $this->logger->log("Moving for store ID: $storeId");
        $this->categoryVersionLogger->logCategoryChange($result, $storeId);
        return $result;
    }
}
