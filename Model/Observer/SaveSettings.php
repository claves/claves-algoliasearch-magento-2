<?php

namespace Algolia\AlgoliaSearch\Model\Observer;

use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;
use Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use Algolia\AlgoliaSearch\Helper\Data;
use Algolia\AlgoliaSearch\Helper\Entity\ProductHelper;
use Algolia\AlgoliaSearch\Model\IndicesConfigurator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Algolia\AlgoliaSearch\Service\ConfigDataStorage;

class SaveSettings implements ObserverInterface
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var IndicesConfigurator */
    protected $indicesConfigurator;

    /**
     * @var AlgoliaHelper
     */
    protected $algoliaHelper;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * @var ConfigDataStorage
     */
    protected $configDataStorage;

    /**
     * @param StoreManagerInterface $storeManager
     * @param IndicesConfigurator $indicesConfigurator
     * @param AlgoliaHelper $algoliaHelper
     * @param Data $helper
     * @param ProductHelper $productHelper
     * @param ConfigDataStorage $configDataStorage
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        IndicesConfigurator $indicesConfigurator,
        AlgoliaHelper $algoliaHelper,
        Data $helper,
        ProductHelper $productHelper,
        ConfigDataStorage $configDataStorage
    ) {
        $this->storeManager = $storeManager;
        $this->indicesConfigurator = $indicesConfigurator;
        $this->algoliaHelper = $algoliaHelper;
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->configDataStorage = $configDataStorage;
    }

    /**
     * @param Observer $observer
     *
     * @throws AlgoliaException
     */
    public function execute(Observer $observer)
    {
         try {
             $storeIds = array_keys($this->storeManager->getStores());
             $oldSortConfigValue = $this->configDataStorage->getValueAndReset('sort_config');
             if ($oldSortConfigValue) {
                 foreach ($storeIds as $storeId) {
                     $indexName = $this->helper->getIndexName($this->productHelper->getIndexNameSuffix(), $storeId);
                     $this->productHelper->handlingReplica($indexName, $storeId, $oldSortConfigValue);
                 }
             }
            foreach ($storeIds as $storeId) {
                $this->indicesConfigurator->saveConfigurationToAlgolia($storeId);
            }
        } catch (\Exception $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
        }
    }
}
