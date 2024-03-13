<?php

namespace Algolia\AlgoliaSearch\Service;

use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Algolia\AlgoliaSearch\Model\Session;

class ConfigDataStorage
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Json
     */
    private $jsonHelper;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ControlValues constructor.
     *
     * @param Session $session
     * @param Json $jsonHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $session,
        Json $jsonHelper,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
    }

    /**
     * Set arbitrary value
     *
     * @param string $name
     * @param mixed $value
     */
    public function setValue(string $name, $value)
    {
        $this->session->setData($name, $value);
    }

    /**
     * Get value
     *
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name)
    {
        return $this->session->getData($name);
    }

    /**
     * Retrieve the value and immediately reset it (unregister it if you will)
     *
     * @param string $name
     * @return mixed
     */
    public function getValueAndReset(string $name)
    {
        return $this->session->getData($name, true);
    }
}

