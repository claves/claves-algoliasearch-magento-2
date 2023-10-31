<?php

namespace Algolia\AlgoliaSearch\Model\CategoryVersion;

use Algolia\AlgoliaSearch\Api\CategoryVersionLoggerInterface;
use Algolia\AlgoliaSearch\Api\CategoryVersionRepositoryInterface;
use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;
use Psr\Log\LoggerInterface;

class CategoryVersionLogger implements CategoryVersionLoggerInterface
{
    public const MIN_CATEGORY_LEVEL = 2;
    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var ConfigHelper
     */
    protected ConfigHelper $config;

    protected StoreManager $storeManager;

    protected CategoryVersionRepositoryInterface $categoryVersionRepository;

    protected array $categoryCache = [];

    public function __construct(
        ConfigHelper                       $config,
        CategoryRepositoryInterface        $categoryRepository,
        CategoryVersionRepositoryInterface $categoryVersionRepository,
        StoreManager                       $storeManager
    )
    {
        $this->config = $config;
        $this->categoryRepository = $categoryRepository;
        $this->categoryVersionRepository = $categoryVersionRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function logCategoryChange(Category $category, int $storedId = 0): void
    {
        if (!$this->config->isCategoryVersionTrackingEnabled($storedId)) return;

        $path = $this->getCategoryPath($category, $storedId);
        ObjectManager::getInstance()->get(LoggerInterface::class)->info("---> Path: $path");

        foreach ($this->getStoreIds($storedId) as $id) {
            /** @var CategoryVersionInterface $version */
            ObjectManager::getInstance()->get(LoggerInterface::class)->info("---> Add log for store $id");
            $version = $this->categoryVersionRepository->getNew();
        }
        // Dedupe existing pending changes for this storeId
        // Insert if not found

    }

    /**
     * @param Category $category
     * @param $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getCategoryPath(Category $category, $storeId): string
    {

        $path = $category->getName();
        foreach (array_slice(array_reverse($category->getPathIds()), 1) as $treeCategoryId) {
            $level = $this->categoryRepository->get($treeCategoryId, $storeId);
            $path = $level->getName() . $this->config->getCategorySeparator() . $path;
            if ((int) $level->getLevel() === self::MIN_CATEGORY_LEVEL) break;
        }
        return $path;
    }

    /**
     * Get applicable store ID's to log versions for
     * @param int $storeId
     * @return array|int[]
     */
    protected function getStoreIds(int $storeId): array
    {
        if ($storeId) return [$storeId];

        $storeIds = [];
        foreach (array_keys($this->storeManager->getStores()) as $id) {
            if ($this->config->isEnabledBackend($id) && $this->config->isCategoryVersionTrackingEnabled($id)) {
                $storeIds[] = $id;
            }
        }
        return $storeIds;
    }
    
    /**
     * @param int $categoryId
     * @param int $storeId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    protected function getCategory(int $categoryId, int $storeId): CategoryInterface
    {
        $key = "$categoryId-$storeId";
        if (!$this->categoryCache[$key]) {
            $this->categoryCache[$key] = $this->categoryRepository->get($categoryId, $storeId);
        }
        return $this->categoryCache[$key];
    }
}
