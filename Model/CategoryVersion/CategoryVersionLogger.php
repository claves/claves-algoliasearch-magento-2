<?php

namespace Algolia\AlgoliaSearch\Model\CategoryVersion;

use Algolia\AlgoliaSearch\Api\CategoryVersionLoggerInterface;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\ObjectManager;
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

    protected array $categoryCache = [];

    public function __construct(
        ConfigHelper                $config,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->config = $config;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritDoc
     */
    public function logCategoryChange(Category $category, int $storedId = 0): void
    {
        if (!$this->config->isCategoryVersionTrackingEnabled($storedId)) return;
        // Build full path from category
        $path = $this->getCategoryPath($category, $storedId);
        ObjectManager::getInstance()->get(LoggerInterface::class)->info("---> Path: $path");
        // Build stores array if !$storeId
        // Iterate through stores array
        // Dedupe existing pending changes for this storeId
        // Insert if not found

    }

    /**
     * @param Category $category
     * @param $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
     * @param int $categoryId
     * @param int $storeId
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
