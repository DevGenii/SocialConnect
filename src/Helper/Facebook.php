<?php

namespace DevGenii\SocialConnect\Helper;

class Facebook extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'devgenii_socialconnect/facebook/enabled';
    const XML_PATH_CLIENT_ID = 'devgenii_socialconnect/facebook/client_id';
    const XML_PATH_CLIENT_SECRET = 'devgenii_socialconnect/facebook/client_secret';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * Facebook constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\App\ConfigInterface $config
    )
    {
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isReadyToUse()
    {
        return $this->isEnabled() && $this->getClientId() && $this->getClientSecret();
    }

    /**
     * @return mixed
     */
    protected function isEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }

    /**
     * @param $xmlPath
     * @return mixed
     */
    protected function getStoreConfig($xmlPath)
    {
        return $this->config->getValue($xmlPath);
    }
}