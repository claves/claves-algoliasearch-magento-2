<?php

namespace Algolia\AlgoliaSearch\Model;

use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class CategoryVersion extends AbstractExtensibleModel implements IdentityInterface, CategoryVersionInterface
{
    const CACHE_TAG = 'algoliasearch_category_version';
    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'algoliasearch_category_version';

    protected function _construct()
    {
        $this->_init( ResourceModel\CategoryVersion::class);
    }

    /**
     * @inheritDoc
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
