<?php

namespace Algolia\AlgoliaSearch\Model\ResourceModel;

class CategoryVersion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'algoliasearch_category_version';
    const ID = 'version_id';
    const CATEGORY_ID = 'entity_id';
    const STORE_ID = 'store_id';
    const OLD_VALUE = 'old_value';
    const NEW_VALUE = 'new_value';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const INDEXED_AT = 'indexed_at';
    const RESOLVED_AT = 'resolved_at';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::ID);
    }
}
