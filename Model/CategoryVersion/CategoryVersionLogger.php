<?php

namespace Algolia\AlgoliaSearch\Model\CategoryVersion;

use Algolia\AlgoliaSearch\Api\CategoryVersionLoggerInterface;
use Algolia\AlgoliaSearch\Api\CategoryVersionRepositoryInterface;
use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;
use Algolia\AlgoliaSearch\Api\Data\CategoryVersionSearchResultsInterface;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Algolia\AlgoliaSearch\Model\ResourceModel\CategoryVersion as CategoryVersionResource;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategorySearchResultsInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Api\SearchCriteriaBuilder;
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

    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /** @var CategoryInterface[] $categoryCache */
    protected array $categoryCache = [];

    public function __construct(
        ConfigHelper                       $config,
        CategoryRepositoryInterface        $categoryRepository,
        CategoryVersionRepositoryInterface $categoryVersionRepository,
        StoreManager                       $storeManager,
        SearchCriteriaBuilder              $searchCriteriaBuilder
    )
    {
        $this->config = $config;
        $this->categoryRepository = $categoryRepository;
        $this->categoryVersionRepository = $categoryVersionRepository;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function logCategoryChange(Category $category, int $storedId = 0): void
    {
        if (!$this->config->isCategoryVersionTrackingEnabled($storedId)) return;

        $path = $this->getNewCategoryPath($category, $storedId);
        ObjectManager::getInstance()->get(LoggerInterface::class)->info("---> Path: $path");

        foreach ($this->getStoreIds($category, $storedId) as $id) {
            /** @var CategoryVersionInterface $version */
            ObjectManager::getInstance()->get(LoggerInterface::class)->info("---> Add log for store $id");
            $version = $this->getCategoryVersion($category->getId(), $path, $id);
            $version->setCategoryId($category->getId());
            $version->setStoreId($id);
            $version->setOldValue($this->getOldCategoryPath($category, $storedId));
            $version->setNewValue($path);
            $version->setUpdatedAt(null);
            $this->categoryVersionRepository->save($version);
        }
    }

    /**
     * Get deduplicated record
     * @param int $categoryId
     * @param string $path
     * @param int $storeId
     * @return CategoryVersionInterface
     */
    protected function getCategoryVersion(int $categoryId, string $path, int $storeId): CategoryVersionInterface {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryVersionResource::CATEGORY_ID, $categoryId)
            ->addFilter(CategoryVersionResource::NEW_VALUE, $path)
            ->addFilter(CategoryVersionResource::STORE_ID, $storeId);
        /* @var CategoryVersionSearchResultsInterface */
        $results = $this->categoryVersionRepository->getList($searchCriteria->create());
        if ($results->getTotalCount()) {
            return array_values($results->getItems())[0];
        } else {
            return $this->categoryVersionRepository->getNew();
        }
    }

    /**
     * @param Category $category
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getNewCategoryPath(Category $category, int $storeId): string
    {
        return $this->getCategoryPath($category->getName(), $category->getPathIds(), $storeId);
    }

    /**
     * @param string $categoryName
     * @param array $pathIds
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getCategoryPath(string $categoryName, array $pathIds, int $storeId): string
    {
        $path = $categoryName;
        foreach (array_slice(array_reverse($pathIds), 1) as $treeCategoryId) {
            $level = $this->getCachedCategory($treeCategoryId, $storeId);
            if ((int) $level->getLevel() < self::MIN_CATEGORY_LEVEL) break;
            $path = $level->getName() . $this->config->getCategorySeparator() . $path;
        }
        return $path;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    protected function getCachedCategory(int $categoryId, int $storeId): CategoryInterface
    {
        $key = "$categoryId-$storeId";
        if (!array_key_exists($key, $this->categoryCache)) {
            $this->categoryCache[$key] = $this->categoryRepository->get($categoryId, $storeId);
        }
        return $this->categoryCache[$key];
    }

    /**
     * For extracting path ids from orig path data
     * @param string|null $path
     * @return int[]
     */
    protected function getPathIds(string|null $path): array
    {
        return $path !== null ? explode('/', $path) : [];
    }

    /**
     * Get applicable store ID's to log versions for - either the specified store or all non overridden stores
     * @param int $storeId
     * @return array|int[]
     * @throws NoSuchEntityException
     */
    protected function getStoreIds(CategoryInterface $category, int $storeId): array
    {
        if ($storeId) return [$storeId];

        $storeIds = [];
        foreach (array_keys($this->storeManager->getStores()) as $id) {
            if ($this->config->isEnabledBackend($id)
                && $this->config->isCategoryVersionTrackingEnabled($id)
                && !$this->isCategoryOverridden($category, $id)) {
                $storeIds[] = $id;
            }
        }
        return $storeIds;
    }

    /**
     * @param CategoryInterface $defaultCategory
     * @param int $storeId
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isCategoryOverridden(CategoryInterface $defaultCategory, int $storeId): bool
    {
        $storeScopedCategory = $this->categoryRepository->get($defaultCategory->getId(), $storeId);
        return $storeScopedCategory->getName() !== $defaultCategory->getName();
    }

    /**
     * @param Category $category
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getOldCategoryPath(Category $category, int $storeId): string
    {
        return $this->getCategoryPath(
            $category->getOrigData(CategoryInterface::KEY_NAME),
            $this->getPathIds($category->getOrigData(CategoryInterface::KEY_PATH)),
            $storeId
        );
    }
}
