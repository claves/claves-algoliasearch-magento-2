<?php

namespace Algolia\AlgoliaSearch\Model\CategoryVersion;

use Algolia\AlgoliaSearch\Api\CategoryVersionRepositoryInterface;
use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterface;
use Algolia\AlgoliaSearch\Api\Data\CategoryVersionInterfaceFactory;
use Algolia\AlgoliaSearch\Model\ResourceModel\CategoryVersion as CategoryVersionResource;

class CategoryVersionRepository implements CategoryVersionRepositoryInterface
{
    /** @var array */
    protected $versions;

    /** @var CategoryVersionInterfaceFactory  */
    protected $versionFactory;

    /**
     * @var CategoryVersionResource
     */
    protected $versionResource;

    public function __construct(
        CategoryVersionInterfaceFactory $versionFactory,
        CategoryVersionResource $versionResource
    ) {
        $this->versionFactory = $versionFactory;
        $this->versionResource = $versionResource;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): CategoryVersionInterface
    {
        if (!isset($this->versions[$id])) {
            $version = $this->versionFactory->create();
            $this->versionResource->load($version, $id);
            if (!$version->getId()) {
                throw new NoSuchEntityException(__("No category version with id $id exists."));
            }
            $this->versions[$version->getId()] = $version;
        }
        return $this->versions[$id];
    }

    /**
     * @inheritDoc
     */
    public function getNew() : CategoryVersionInterface {
        return $this->versionFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function save(CategoryVersionInterface $version): CategoryVersionInterface
    {
        $this->versionResource->save($version);
        return $version;
    }

    /**
     * @inheritDoc
     */
    public function delete(CategoryVersionInterface $version) : void
    {
        $this->versionResource->delete($version);
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id) : void
    {
        $version = $this->getById($id);
        unset($this->versions[$id]);
        $this->delete($version);
    }


}
